#include <stdio.h>

int digit[10] = {0,0,0,0,0,0,0,0,0,0};
long diff[10] = {-387420489,1,3,23,229,2869,43531,776887,15953673,370643273};
 
int main(void)
{
  unsigned long x = 0, val = 0;
  int i;
  while (x < 4e9) {
    if (x == val) printf("%lu\n", x);
    i = 0;
    digit[i] = digit[i] == 9? 0 : digit[i]+1;
    val += diff[digit[i]];
    while (digit[i] == 0) {
      i++;
      digit[i] = digit[i] == 9? 0 : digit[i]+1;
      val += diff[digit[i]];
    }
    x++;
  }
  return 0;
}
