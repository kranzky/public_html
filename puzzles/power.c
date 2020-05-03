
/*===========================================================================*/

/*
 *  Copyright (C) 1997 Jason Hutchens
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
 *		File:			Power.c
 *
 *		Program:		Power Summation Puzzle Solver
 *
 *		Purpose:		To find all integers which satisfy the following
 *						property: the sum of the digits raised to the power
 *						of the digit is equal to the original number.  For
 *						example, 3435 satisfies this property, as 3^3 + 4^4 +
 *						3^3 + 5^5 = 3435.
 *
 *		Author:		Mr. Jason L. Hutchens
 *
 *		WWW:			http://ciips.ee.uwa.edu.au/~hutch/puzzles/Power.html
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

#include <stdio.h>

/*
 *		Define an integer as an unsigned 32-bit number.
 */
typedef unsigned long INT;

int main(int argc, char **argv)
{
	INT number;
	INT sum;
	INT num;
	INT power[]={1,1,4,27,256,3125,46656,823543,16777216,387420489};

	for(number=0; number<4e9; ++number) {

		sum=0;
		num=number;

		/*
		 *		Calculate the sum of the digits in the current number.
		 *		We get the digits by dividing the number by 10 and
		 *		indexing the power[] array by the remainder of the
		 *		division.
		 *
		 *		If this sum exceeds the original number, then we can
		 *		halt the process prematurely.  Otherwise we must see
		 *		it through to the end, and then compare the sum with
		 *		the number.
		 */
		do {
			if((sum+=power[num%10])>number) break;
		} while(num/=10);

		if(number==sum) printf("%d\n", number);

	}

	return(0);
}

/*===========================================================================*/

