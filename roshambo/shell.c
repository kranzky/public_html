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
 *		Name:		RoShamBo Shell
 *
 *		Author:	Jason Hutchens (hutch@amristar.com.au)
 *
 *		Purpose:	A simply shell for a RoShamBo algorithm, which allows a human
 *					being to play against the computer.  Compile the program as
 *					follows:
 *
 *						gcc -o roshambo shell.c megahal.c -Wall -lm -lcurses
 *
 *					You can replace megahal.c with a source file containing any
 *					RoShamBo algorithm, provided that you replace the references
 *					to "halbot" in this file with the name of your function.
 *
 *		$Id: shell.c,v 1.2 1999/09/13 06:02:23 hutch Exp hutch $
 */
/*============================================================================*/

#include <stdio.h>
#include <stdlib.h>
#include <ctype.h>
#include <malloc.h>
#include <assert.h>
#include <curses.h>
#include <time.h>
#include <unistd.h>

/*============================================================================*/

#define	rock		0
#define	paper		1
#define	scissors	2

/*============================================================================*/

extern int halbot(void);

/*============================================================================*/

int my_history[1000];
int opp_history[1000];
char *name[]={"Rock", "Paper", "Scissors"};
char user[L_cuserid];

/*============================================================================*/
/*
 *		Function:	main
 *
 *		Arguments:	int argc, char **argv
 *
 *		Returns:		0 if successful.
 *
 *		Overview:	Run a RoShamBo contest.  The human opponent can type R, P
 *						or S to select rock, paper or scissors.  The machine then
 *						makes it's choice.  Press V at any time for statistics, and
 *						Q at any time to quit.
 */
int main(int argc, char **argv)
{
	int num_games;
	int my_wins;
	int my_move;
	int opp_wins;
	int opp_move;

	/*
	 *		Get the name of the human player.
	 */
	if(cuserid(user)==NULL) strcpy(user, "Human");
	user[0]=toupper(user[0]);
	/*
	 *		Initialise curses.
	 */
	initscr();
	cbreak();
	noecho();
	idlok(stdscr, 1);
	scrollok(stdscr, 1);
	/*
	 *		Initialise the history arrays.
	 */
	my_history[0]=0;
	opp_history[0]=0;
	/*
	 *		Initialise the score table.
	 */
	num_games=0;
	my_wins=0;
	opp_wins=0;
	/*
	 *		Run the contest.
	 */
	while(1) {
		/*
		 *		Prompt the user to make a move.
		 */
		printw("One, two, three... "); refresh();
		my_move=-1;
		/*
		 *		See what move they make.
		 */
		do switch(toupper(getchar())) {
			case 'R':
				my_move=rock;
				break;
			case 'P':
				my_move=paper;
				break;
			case 'S':
				my_move=scissors;
				break;
			case 'Q':
				/*
				 *		If they want to quit, prompt them before exiting or not.
				 */
				printw("\nAre you sure?  [Y/N] "); refresh();
				do switch(toupper(getchar())) {
					case 'Y':
						endwin();
						return(0);
					case 'N':
						printw("\nOne, two, three... "); refresh();
						break;	
					default:
						continue;
				} while(0);
				continue;
			default:
				/*
				 *		If they hit an invalid key, offer some help.
				 */
				printw("\nPlease type R,P,S or Q: "); refresh();
				continue;
		} while(my_move==-1);
		/*
		 *		If it's the first game, seed the random number generator.
		 */
		if(num_games==0) srandom(time(NULL));
		/*
		 *		If it's the last game, print a message and exit.
		 */
		if(num_games==999) {
			printw("999 Games Played.  MegaHAL got bored!\n"); refresh();
			endwin();
			return(0);
		}
		/*
		 *		Ask MegaHAL to make its move.
		 */
		opp_move=halbot();
		/*
		 *		Update the two history arrays.
		 */
		my_history[0]=num_games;
		opp_history[0]=num_games;
		my_history[num_games]=opp_move;
		opp_history[num_games]=my_move;
		/*
		 *		Print the results of this round.
		 */
		num_games+=1;
		printw("\n%s chose %s, ", user, name[my_move]); refresh();
		printw("MegaHAL chose %s.  ", name[opp_move]); refresh();
		if((my_move+2)%3==opp_move) {
			printw("%s wins!\n", user); refresh();
			my_wins+=1;
		} else if((my_move+1)%3==opp_move) {
			printw("MegaHAL wins!\n"); refresh();
			opp_wins+=1;
		} else {
			printw("It's a draw!\n"); refresh();
		}
		printw("SCORE: MegaHAL %d, %s %d (%d games played)\n", opp_wins, user,
			my_wins, num_games);
		refresh();
	}

}	/* end of "main" */

/*============================================================================*/
/*
 *		$Log: shell.c,v $
 *		Revision 1.2  1999/09/13 06:02:23  hutch
 *		Final version, some bugs squished.
 *
 *		Revision 1.1  1999/09/13 03:24:14  hutch
 *		Initial revision
 */
/*============================================================================*/
