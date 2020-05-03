#
#  Copyright (C) 1998 Jason Hutchens
#
#  This program is free software; you can redistribute it and/or modify it
#  under the terms of the GNU General Public License as published by the Free
#  Software Foundation; either version 2 of the license or (at your option)
#  any later version.
#
#  This program is distributed in the hope that it will be useful, but
#  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
#  or FITNESS FOR A PARTICULAR PURPOSE.  See the Gnu Public License for more
#  details.
#
#  You should have received a copy of the GNU General Public License along
#  with this program; if not, write to the Free Software Foundation, Inc.,
#  675 Mass Ave, Cambridge, MA 02139, USA.
#

all:	megahal

megahal:
	@echo "Please use 'make system', where system is one of the following:"
	@echo "- sunos or sunos_debug"
	@echo "- irix"
	@echo "- linux, linux_dbmalloc, linux_tcl or linux_debug"
	@echo "- osf"

sunos: megahal.c megahal.h
	gcc -o megahal_sunos megahal.c -lm -DSUNOS
	@echo "MegaHAL for SunOS is up to date"

sunos_debug: megahal.c megahal.h debug.c debug.h
	gcc -o megahal_sunos megahal.c debug.c -lm -DSUNOS -DDEBUG
	@echo "MegaHAL (debug) for SunOS is up to date"

irix: megahal.c megahal.h
	cc -o megahal_irix megahal.c -lm -DIRIX
	@echo "MegaHAL for IRIX is up to date"

new: megahal_new.c megahal_new.h
	cc -o megahal_new megahal_new.c -lm -DIRIX
	@echo "New MegaHAL for IRIX is up to date"

linux: megahal.c megahal.h
	gcc -o megahal_linux megahal.c -lm -DLINUX
	@echo "MegaHAL for Linux is up to date"

linux_debug: megahal.c megahal.h debug.c debug.h
	gcc -o megahal_linux megahal.c debug.c -lm -DLINUX -DDEBUG
	@echo "MegaHAL (debug) for Linux is up to date"

linux_dbmalloc: megahal.c megahal.h
	gcc -o megahal_linux megahal.c -lm -DLINUX -ldbmalloc -L. -I.
	@echo "MegaHAL (dbmalloc) for Linux is up to date"

linux_tcl: megahal.c megahal.h
	gcc -o megahal_linux_tcl megahal.c -ltcl8.0 -DLINUX -DTCL
	@echo "MegaHAL (tcl) for Linux is up to date"

osf: megahal.c megahal.h
	cc -o megahal_osf megahal.c -lm -DOSF
	@echo "MegaHAL for Alpha is up to date"

#
#	$Log: Makefile,v $
#	Revision 1.2  1998/04/21 10:10:56  hutch
#	Updated.
#
#	Revision 1.1  1998/04/06 08:03:34  hutch
#	Initial revision
#
