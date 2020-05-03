/*============================================================================*/
/*
 *  Copyright (C) 1999 Jason Hutchens
 *
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the GNU General Public License as published by the Free
 *  Software Foundation; either version 2 of the license or (at your option)
 *  any later version.
 *
 *  This program is distributed in the hope that it will be useful, but
 *  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *  or FITNESS FOR A PARTICULAR PURPOSE.  See the Gnu Public License for more
 *  details.
 *
 *  You should have received a copy of the GNU General Public License along
 *  with this program; if not, write to the Free Software Foundation, Inc.,
 *  675 Mass Ave, Cambridge, MA 02139, USA.
 */
/*============================================================================*/
/*
 *		NB:		File displays best with tabstops set to three spaces.
 *
 *		Name:		MegaHAL (in honour of http://ciips.ee.uwa.edu.au/~hutch/hal/)
 *
 *		Author:	Jason Hutchens (hutch@amristar.com.au)
 *
 *		Purpose:	Play the game of Rock-Paper-Scissors.  Statistics about the
 *					game so far are recorded in a ternary trie, represnting an
 *					infinite-order Markov model.  The context which has performed
 *					best in the past is used to make the prediction, and we
 *					gradually fall-back through contexts which performed less well
 *					when the contexts haven't yet been observed.  One of the
 *					contexts is always guaranteed to make a move at random, so
 *					we never encounter a situation where we can't make a move.
 *					Statistics are gathered over a sliding window, allowing
 *					adaption if the opponent's strategies change.
 *
 *		$Id: megahal.c,v 1.8 1999/09/16 03:18:27 hutch Exp hutch $
 */
/*============================================================================*/

#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <malloc.h>
#include <assert.h>
#include <sys/time.h>
#include <unistd.h>

/*============================================================================*/

#define rock		0
#define paper		1
#define scissors	2

/*============================================================================*/

int halbot(void);
static int halbot_compare(const void *, const void *);

/*============================================================================*/
/*
 *		Function:	halbot
 *
 *		Arguments:	void
 *
 *		Returns:		An integer between 0 and 2, representing the move that the
 *						predictor makes in the game of Rock-Paper-Scissors.
 *
 *		Overview:	The program collects statistics about the game using an
 *						infinite context Markov model, which is stored in a ternary
 *						trie.  The procedure is to update the statistics of the
 *						model with the latest moves, and remove the statistics of
 *						moves outside a sliding window of defined length.  We build
 *						an array of contexts which make valid predictions, including
 *						the special context which always predicts at random, and we
 *						sort this according to how well the contexts have performed
 *						in the recent past (again with the sliding window).  The
 *						best context is then used to make the prediction, and our
 *						move is selected in order to maximise the expected value of
 *						the score.  The bot monitors how much time it's been
 *						spending, and emits a message when this time exceeds an
 *						average of one millisecond per move (i.e. one second per
 *						one thousand moves).
 *
 *		Comment:		Even though it's really messy, everything is done in this
 *						one function to allow it to be added to the competition
 *						source code easily.
 */
int halbot(void)
{
	/*
	 *		Set this to a non-zero value to emit an warning message if the
	 *		algorithm averages more than one millisecond per move.
	 */
	#define	ERROR		0
	/*
	 *		These defines are the three heuristic parameters that can be modified
	 *		to alter performance.  BELIEVE gives the number of times a context
	 *		must be observed before being used for prediction, HISTORY gives the
	 *		maximum context size to observe (we're limited by time, not memory),
	 *		and WINDOW gives the size of the sliding window, 0 being infinite.
	 *
	 *		- BELIEVE>=1
	 *		- HISTORY>=1
	 *		- WINDOW>=HISTORY or 0 for infinite
	 */
	#define	BELIEVE	1
	#define	HISTORY	21
	#define	WINDOW	200
	/*
	 *		This define just makes the code neater (huh, as if).
	 */
	#define	COUNT		trie[context[i].node].move
	#define	SCOUNT	trie[sorted[i].node].move
	/*
	 *		These macros returns the maximum/minimum values of two expressions.
	 */
	#define	MAX(a,b)	(((a)>(b))?(a):(b))
	#define	MIN(a,b)	(((a)<(b))?(a):(b))
	/*
	 *		Each node of the trie contains frequency information about the moves
	 *		made at the context represented by the node, and where the sequent
	 *		nodes are in the array.
	 */
	typedef struct S_NODE {
		short int total;
		short int move[3];
		int next[3];
	} NODE;
	/*
	 *		The context array contains information about contexts of various
	 *		lengths, and this is used to select a context to make the prediction.
	 */
	typedef struct S_CONTEXT {
		int worst;
		int best;
		int seen;
		int size;
		int node;
	} CONTEXT;
	/*
	 *		This is the only external information we have about our opponent;
	 *		it's a history of the game so far.
	 */
	extern int my_history[];
	extern int opp_history[];
	/*
	 *		We declare all variables as statics because we want most of them to
	 *		be persistent.
	 */
	static int move=-1;
	static int last_move=-1;
	static int random_move=-1;
	static NODE *trie=NULL;
	static int trie_size=0;
	static int context_size=0;
	static CONTEXT *context=NULL;
	static CONTEXT *sorted=NULL;
	static int **memory=NULL;
	static int remember=0;
	static struct timeval start;
	static struct timeval end;
	static long think;
	static int node;
	static int expected[3];
	/*
	 *		But you canny have static register variables!
	 */
	register int i,j;
	/*
	 *		Start the timer.
	 */
	(void)gettimeofday(&start,NULL);
	/*
	 *		If this is the beginning of the game, set some things up.
	 */
	if(my_history[0]==0) {
		if(trie==NULL) {
			/*
			 *		If this is the first game we've played, initialise the memory.
			 *		On some Unices, realloc doesn't work with NULL arguments, so
			 *		we're just making sure they're non-NULL.
			 *
			 *		NB: We must allocate two elements for the context!
			 */
			context=(CONTEXT *)malloc(sizeof(CONTEXT)*(HISTORY+2));
			assert(context);
			sorted=(CONTEXT *)malloc(sizeof(CONTEXT)*(HISTORY+2));
			assert(sorted);
			if(WINDOW>0) {
				memory=(int **)malloc(sizeof(int *)*WINDOW);
				assert(memory);
				for(i=0; i<WINDOW; ++i) {
					memory[i]=(int *)malloc(sizeof(int)*(HISTORY+2));
					assert(memory[i]);
				}
			}
			trie=(NODE *)malloc(sizeof(NODE));
			assert(trie);
		}
		/*
		 *		Clear the trie, by setting its size to unity, and clearing the
		 *		statistics of the root node.
		 */
		trie_size=1;
		trie[0]=(NODE){0,{0,0,0},{-1,-1,-1}};
		/*
		 *		Clear the memory matrix.
		 */
		for(i=0; i<WINDOW; ++i)
			for(j=0; j<HISTORY+2; ++j)
				memory[i][j]=-1;
		/*
		 *		Clear the context array.
		 */
		for(i=0; i<HISTORY+2; ++i) context[i]=(CONTEXT){0,0,0,0,0};
		context[0]=(CONTEXT){0,0,0,-1,-1};
		context[1]=(CONTEXT){0,0,0,0,0};
		/*
		 *		Clear the variable we use to keep track of how long MegaHAL
		 *		spends thinking.
		 */
		think=0;
	}
	/*
	 *		If we've already started playing, evaluate how well we went on our
	 *		last turn, and update our list which decides which contexts give the
	 *		best predictions.
	 */
	if(my_history[0]>0) {
		/*
		 *		We begin by forgetting which contexts performed well in the
		 *		distant past.
	 	 */
		if(WINDOW>0) for(i=1; i<context_size; ++i) {
			if(WINDOW-i>0) {
				if(memory[(remember+i-1)%WINDOW][i]>=0) {
					if(memory[(remember+i-1)%WINDOW][i]==
						((opp_history[my_history[0]-WINDOW+i-1]+1)%3))
							context[i].best-=1;
					if(memory[(remember+i-1)%WINDOW][i]==
						((opp_history[my_history[0]-WINDOW+i-1]+2)%3))
							context[i].worst-=1;
					context[i].seen-=1;
				}
			}
		}
		/*
		 *		Clear our dimmest memory.
		 */
		if(WINDOW>0) for(i=0; i<context_size; ++i)
			memory[remember][i]=-1;
		/*
		 *		We then remember the contexts which performed the best most
		 *		recently.
		 */
		for(i=0; i<context_size; ++i) {
			if(context[i].node>=trie_size) continue;
			if(context[i].node==-1) continue;
			if(trie[context[i].node].total>=BELIEVE) {
				for(j=0; j<3; ++j)
					expected[j]=COUNT[(j+2)%3]-COUNT[(j+1)%3];
				last_move=0;
				for(j=1; j<3; ++j)
					if(expected[j]>expected[last_move])
						last_move=j;
				if(last_move==(opp_history[my_history[0]]+1)%3)
					context[i].best+=1;
				if(last_move==(opp_history[my_history[0]]+2)%3)
					context[i].worst+=1;
				context[i].seen+=1;
				if(WINDOW>0) memory[remember][i]=last_move;
			}
		}
		if(WINDOW>0) remember=(remember+1)%WINDOW;
	}
	/*
	 *		Clear the context to the node which always predicts at random, and
	 *		the root node.
	 */
	context_size=2;
	/*
	 *		We begin by forgetting the statistics we've previously gathered
	 *		about the symbol which is now leaving the sliding window.
	 */
	if((WINDOW>0)&&(my_history[0]-WINDOW>0))
		for(i=my_history[0]-WINDOW;
			i<MIN(my_history[0]-WINDOW+HISTORY,my_history[0]); ++i) {
		/*
		 *		Find the node which corresponds to the context.
		 */
		node=0;
		for(j=MAX(my_history[0]-WINDOW,1); j<i; ++j) {
			node=trie[node].next[opp_history[j]];
			node=trie[node].next[my_history[j]];
		}
		if((node<0)||(node>=trie_size))fprintf(stderr, "OUCH\n");
		/*
		 *		Update the statistics of this node with the opponents move.
		 */
		trie[node].total-=1;
		trie[node].move[opp_history[i]]-=1;
	}
	/*
	 *		Build up a context array pointing at all the nodes in the trie
	 *		which predict what the opponent is going to do for the current
	 *		history.  While doing this, update the statistics of the trie with
	 *		the last move made by ourselves and our opponent.
	 */
#if(WINDOW>0)
	for(i=my_history[0]; i>MAX(my_history[0]-MIN(WINDOW,HISTORY),0); --i) {
#else
	for(i=my_history[0]; i>MAX(my_history[0]-HISTORY,0); --i) {
#endif
		/*
		 *		Find the node which corresponds to the context.
		 */
		node=0;
		for(j=i; j<my_history[0]; ++j) {
			node=trie[node].next[opp_history[j]];
			node=trie[node].next[my_history[j]];
		}
		if((node<0)||(node>=trie_size))fprintf(stderr, "OUCH\n");
		/*
		 *		Update the statistics of this node with the opponents move.
		 */
		trie[node].total+=1;
		trie[node].move[opp_history[my_history[0]]]+=1;
		/*
		 *		Move to this node, making sure that we've allocated it first.
		 */
		if(trie[node].next[opp_history[my_history[0]]]==-1) {
			trie[node].next[opp_history[my_history[0]]]=trie_size;
			trie_size+=1;
			trie=(NODE *)realloc(trie,sizeof(NODE)*trie_size);
			assert(trie);
			trie[trie_size-1]=(NODE){0,{0,0,0},{-1,-1,-1}};
		}
		node=trie[node].next[opp_history[my_history[0]]];
		if((node<0)||(node>=trie_size))fprintf(stderr, "OUCH\n");
		/*
		 *		Move to this node, making sure that we've allocated it first.
		 */
		if(trie[node].next[my_history[my_history[0]]]==-1) {
			trie[node].next[my_history[my_history[0]]]=trie_size;
			trie_size+=1;
			trie=(NODE *)realloc(trie,sizeof(NODE)*trie_size);
			assert(trie);
			trie[trie_size-1]=(NODE){0,{0,0,0},{-1,-1,-1}};
		}
		node=trie[node].next[my_history[my_history[0]]];
		if((node<0)||(node>=trie_size))fprintf(stderr, "OUCH\n");
		/*
		 *		Add this node to the context array.
		 */
		context_size+=1;
		context[context_size-1].node=node;
		context[context_size-1].size=context_size-2;
	}
	/*
	 *		Sort the context array, based upon what contexts have proved
	 *		successful in the past.  We sort the context array by looking
	 *		at the context lengths which most often give the best predictions.
	 *		If two contexts give the same amount of best predictions, choose
	 *		the longest one.  If two contexts have consistently failed in the
	 *		past, choose the shortest one.
	 */
	for(i=0; i<context_size; ++i)
		sorted[i]=context[i];
	qsort(sorted,context_size,sizeof(CONTEXT),halbot_compare);
	/*
	 *		Starting at the most likely context, gradually fall-back until we
	 *		find a context which has been observed at least BELIEVE times.  Use
	 *		this context to predict a probability distribution over the opponents
	 *		possible moves.  Select the move which maximises the expected score.
	 */
	move=-1;
	for(i=0; i<context_size; ++i) {
		if(sorted[i].node>=trie_size) continue;
		if(sorted[i].node==-1) break;
		if(trie[sorted[i].node].total>=BELIEVE) {
			for(j=0; j<3; ++j)
				expected[j]=SCOUNT[(j+2)%3]-SCOUNT[(j+1)%3];
			move=0;
			for(j=1; j<3; ++j)
				if(expected[j]>expected[move])
					move=j;
			break;
		}
	}
	/*
	 *		If no prediction was possible, make a random move.
	 */
	random_move=random()%3;
	if(move==-1) move=random_move;
	/*
	 *		Update the timer, and warn if we've exceeded one second per one
	 *		thousand turns.
	 */
	(void)gettimeofday(&end,NULL);
	if(think>=0)
		think+=1000000*(end.tv_sec-start.tv_sec)+(end.tv_usec-start.tv_usec);
	if((ERROR!=0)&&((think/(my_history[0]+1)>=1000)&&(my_history[0]>100))) {
		think=-1;
		fprintf(stdout, "\n\n*** MegaHAL-Infinite is too slow! ***\n\n");
	}
	/*
	 *		Return our move.
	 */
	return(move);

}	/* end "halbot" */

/*----------------------------------------------------------------------------*/
/*
 *		Function:	halbot_compare
 *
 *		Arguments:	const void *value1, const void *value2
 *						Two pointers into the sort array.  Our job is to decide
 *						whether value1 is less than, equal to or greater than
 *						value2.
 *
 *		Returns:		An integer which is positive if value1<value2, negative if
 *						value1>value2, and zero if they're equal.  Various heuristics
 *						are used to test for this inequality.
 *
 *		Overview:	This comparison function is required by qsort().
 */
static int halbot_compare(const void *value1, const void *value2)
{
	/*
	 *		This is a duplicate of the typedef in halbot(), put here to avoid
	 *		having to make it external to the functions.
	 */
	typedef struct S_CONTEXT {
		int worst;
		int best;
		int seen;
		int size;
		int node;
	} CONTEXT;
	/*
	 *		Some local variables.
	 */
	CONTEXT *context1;
	CONTEXT *context2;
	float prob1=0.0;
	float prob2=0.0;
	/*
	 *		This looks complicated, but it's not.  Basically, given two nodes
	 *		in the trie, these heuristics decide which node should be used to
	 *		make a prediction first.  The rules are:
	 *		- Choose the one which has performed the best in the past.
	 *		- If they're both being tried for the first time, choose the shortest.
	 *		- If they've both performed equally well, choose the longest.
	 */
	context1=(CONTEXT *)value1;
	context2=(CONTEXT *)value2;
	if(context1->seen>0)
		prob1=(float)(context1->best-context1->worst)/(float)(context1->seen);
	if(context2->seen>0)
		prob2=(float)(context2->best-context2->worst)/(float)(context2->seen);
	if(prob1<prob2) return(1);
	if(prob1>prob2) return(-1);
	if((context1->seen==0)&&(context2->seen=0)) {
		if(context1->size<context2->size) return(-1);
		if(context1->size>context2->size) return(1);
		return(0);
	}
	if(context1->size<context2->size) return(1);
	if(context1->size>context2->size) return(-1);
	return(0);

}	/* end of "halbot_compare" */

/*============================================================================*/
/*
 *		$Log: megahal.c,v $
 *		Revision 1.7  1999/09/16 03:16:55  hutch
 *		Did some speed improvements, improved the method of remembering past
 *		strategies, and imroved the heuristics for sorting.  Over 1000 tourneys
 *		of 1000 trials, it performed 17.6 times better than the second best bot,
 *		"Beat Last Move", and scored an average of 678 per match.  It also
 *		consistently beats version 1.1, scoring an average of 100 or so per
 *		match.
 *
 *		Revision 1.5  1999/09/13 16:51:57  hutch
 *		The sliding window is working perfectly.  Of course, this strategy
 *		doesn't improve the performance of MegaHAL-Infinite on the standard
 *		algorithms, but it will hopefully improve performance on smarter ones.
 *
 *		Revision 1.4  1999/09/13 14:48:57  hutch
 *		Cleaned up the source a bit, and prepared to implement the sliding
 *		window strategy.
 *
 *		Revision 1.3  1999/09/12 06:29:30  hutch
 *		Consideration of the statistics, and correcting it to give proper
 *		probability estimates, improved Megahal-Infinite beyond MegaHAL.
 *
 *		Revision 1.2  1999/09/12 06:23:02  hutch
 *		Infinite contexts are done, and we now choose the context that has
 *		performed the best in the past.  Doesn't perform as well as MegaHAL,
 *		but I believe it will perform better on craftier algorithms.  Plus
 *		it out-performs MegaHAL on R-P-S 20-20-60.
 *
 *		Revision 1.1  1999/09/12 03:53:08  hutch
 *		This is the first official version.  We are now going to concentrate
 *		on making an infinite-context model, and collecting statistics over
 *		a sliding window, in the hope that this will improve performance
 *		against more sophisticated algorithms.
 *
 *		Revision 0.4  1999/09/11 12:40:11  hutch
 *		Okay, experimentation with parameters has increased it's performance to
 *		about 15 times better than the second best bot, and it's near perfect on
 *		"Beat Last Move", "Beat Frequent Pick", "Rotate RPS" and "Good Ol Rock".
 *		It scores about half on "Always Switchin'", and about a third on "R-P-S
 *		20-20-60".  Interestingly, this is the only bot which it has difficulty
 *		with.  Over 1000 tourneys of 1000 trials, it performed 17.5 times better
 *		than the second best bot, "Beat Last Move", and scored an average of 677
 *		per match.
 *
 *		Revision 0.3  1999/09/11 12:33:54  hutch
 *		Everything is working; the program kicks ass against the standard bots
 *		(performing at least twelve times better than the second best).  I will
 *		fine-tune the algorithm a bit, although it is quite quick, and will play
 *		around with the heuristics before submitting.
 *
 *		Revision 0.2  1999/09/11 11:40:01  hutch
 *		The mechanism for selecting the best move has been finished, and the
 *		model is working for a NULL context.  Now we need to expand it to the
 *		infinite context.
 *
 *		Revision 0.1  1999/09/11 05:58:29  hutch
 *		Initial revision
 */
/*============================================================================*/
