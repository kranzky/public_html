/*
	Copyright (C) 1999 Vincent L�nngren under the conditions described below,
	that is conditions set forth by the GNU General Public License.
	
	Some changes were made by me, the latest one in 1999-09-27. This include one
	bug fix, some Amiga specific changes and several minor refinements. None of
	the original algorithm was changed.Contact: Vincentpojken@2.sbbs.se
*/

/*===========================================================================*/

/*
 *  Copyright (C) 1998 Jason Hutchens
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

/*===========================================================================*/

/*
 *		$Id: megahal.h,v 1.3 1998/09/03 03:15:40 hutch Exp hutch $
 *
 *		File:			megahal.h
 *
 *		Program:		MegaHAL v8r5
 *
 *		Purpose:		To simulate a natural language conversation with a psychotic
 *						computer.  This is achieved by learning from the user's
 *						input using a third-order Markov model on the word level.
 *						Words are considered to be sequences of characters separated
 *						by whitespace and punctuation.  Replies are generated
 *						randomly based on a keyword, and they are scored using
 *						measures of surprise.
 *
 *		Author:		Mr. Jason L. Hutchens
 *
 *		WWW:			http://ciips.ee.uwa.edu.au/~hutch/hal/
 *
 *		E-Mail:		hutch@ciips.ee.uwa.edu.au
 *
 *		Contact:		The Centre for Intelligent Information Processing Systems
 *						Department of Electrical and Electronic Engineering
 *						The University of Western Australia
 *						AUSTRALIA 6907
 *
 *		Phone:		+61-8-9380-3856
 *
 *		Facsimile:	+61-8-9380-1168
 *
 *		Notes:		This file is best viewed with tabstops set to three spaces.
 */

/*===========================================================================*/
#define P_THINK 40

#if defined(AMIGA_OLD) || defined(AMIGA_V38)
#define D_KEY 5
#define V_KEY 2
#define D_THINK 10
#define V_THINK 5
#else
#define D_KEY 100000
#define V_KEY 50000
#define D_THINK 500000
#define V_THINK 250000
#endif

#define MIN(a,b) ((a)<(b))?(a):(b)

#define COOKIE "MegaHALv8"

#if defined (AMIGA_OLD) || defined(AMIGA_V38)
#define DEFAULT ""
#else
#define DEFAULT "."
#endif

#define COMMAND_SIZE (sizeof(command)/sizeof(command[0]))

#define BYTE1 unsigned char
#define BYTE2 unsigned short
#define BYTE4 unsigned long

#ifdef __mac_os
#define bool Boolean
#endif

#ifdef DOS
#define SEP "\\"
#else
#define SEP "/"
#endif

#if defined(AMIGA_OLD) || defined(AMIGA_V38)
#undef SEP
#define SEP ""
#define usleep(x) Delay((long) x)
#endif

#ifdef AMIGA_V38
#undef toupper
#define toupper(x) ToUpper((unsigned long) x)
#undef tolower
#define tolower(x) ToLower((unsigned long) x)
#undef isalpha
#define isalpha(x) IsAlpha(_AmigaLocale,(unsigned long) x)
#undef isalnum
#define isalnum(x) IsAlNum(_AmigaLocale,(unsigned long) x)
#undef isdigit
#define isdigit(x) IsDigit(_AmigaLocale,(unsigned long) x)
#undef isspace
#define isspace(x) IsSpace(_AmigaLocale,(unsigned long) x)
#endif

/*===========================================================================*/

#ifndef __mac_os
#undef FALSE
#undef TRUE
typedef enum { FALSE, TRUE } bool;
#endif

typedef struct {
	BYTE1 length;
	char *word;
} STRING;

typedef struct {
	BYTE4 size;
	STRING *entry;
	BYTE2 *index;
} DICTIONARY;

typedef struct {
	BYTE2 size;
	STRING *from;
	STRING *to;
} SWAP;

typedef struct NODE {
	BYTE2 symbol;
	BYTE4 usage;
	BYTE2 count;
	BYTE2 branch;
	struct NODE **tree;
} TREE;

typedef struct {
	BYTE1 order;
	TREE *forward;
	TREE *backward;
	TREE **context;
	DICTIONARY *dictionary;
} MODEL;

typedef enum { UNKNOWN, QUIT, EXIT, SAVE, DELAY, HELP, SPEECH, VOICELIST, VOICE, BRAIN, PROGRESS, THINK } COMMAND_WORDS;

typedef struct {
	STRING word;
	char *helpstring;
	COMMAND_WORDS command;
} COMMAND;

/*===========================================================================*/

#ifdef SUNOS
extern double drand48(void);
extern void srand48(long);
#endif


/*===========================================================================*/

/* See also top of file */

/*
 *		$Log: megahal.h,v $
 * Revision 1.3  1998/09/03  03:15:40  hutch
 * Dunno.
 *
 *		Revision 1.2  1998/04/21 10:10:56  hutch
 *		Fixed a few little errors.
 *
 *		Revision 1.1  1998/04/06 08:02:01  hutch
 *		Initial revision
 */

/*===========================================================================*/