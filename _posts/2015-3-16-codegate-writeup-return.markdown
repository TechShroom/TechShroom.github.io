---
layout: post
title: CodeGate 2015/Writeup - return
---

This was a 150 point miscellaneous problem.

The binary simply opens up the flag and reads it, then reads your input and calls `memcmp(flag, argv[1], strlen(argv[1]));`. It then sleeps for 3 seconds to prevent a brute force, and returns the result of `memcmp`.

By implementing a binary search using the return value of `return` as the compare, the flag can be narrowed down to its value.

Command: Something that did a binary search with `./return`.

Flag: `hohoqqqz`

`binaryreturn.py` (target: Python 2.7):
```
import subprocess
def bsearch(regstr):
    first = ord(' ')
    last = ord('~')
    found = 'NOPE'

    while first<=last and found=='NOPE':
        midpoint = (first + last)//2
        midstr = chr(midpoint)
        print 'testing:',midstr,' [onthewhole:',regstr+midstr,']'
        ret = subprocess.call(['./return', regstr+midstr])
        print 'test:',midstr,'is',ret
        if ret == 0:
            found = midstr
        elif ret > 127:
            last = midpoint-1
        else:
            first = midpoint+1

    return found

import os
os.chdir('/home/return')
s = 'h'
while len(s)<10:
    s += bsearch(s)
    print 'on:',s
print s
```
