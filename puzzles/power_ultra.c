#include <stdio.h>
 
typedef unsigned long   ulong;
 
long diffs[] = {
    1, 3, 23, 229, 2869, 43531, 776887, 15953673, 370643273, -387420489
};
 
main()
{
    ulong       sum, n; 
    int         i0,i1,i2,i3,i4,i5,i6,i7,i8,i9;
 
    sum = 0;
    n = 0;
    
#define LOOP(var, lim, code)	\
    for (var = 0; var < lim; sum += diffs[var++])	code
 
    LOOP(i9, 4,
    LOOP(i8, 10,
    LOOP(i7, 10,
    LOOP(i6, 10,
    LOOP(i5, 10,
    LOOP(i4, 10,
    LOOP(i3, 10,
    LOOP(i2, 10,
    LOOP(i1, 10,
    LOOP(i0, 10,
    {
        if (n == sum)
            printf("%lu\n", n);
        n++;
    }
    ))))))))))
    return 0;
}
