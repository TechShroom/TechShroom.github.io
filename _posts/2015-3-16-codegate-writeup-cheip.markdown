---
layout: post
title: CodeGate 2015/Writeup - cheip
---

This was a 100 point pwnable problem.

The binary has a built-in backdoor function, all you have to do is get the `bof` function to return there with a binary overflow.

However, one of the requirements is that the input starts with `"can_you_do_bof"`. Not really a problem in the long run, as it just gives more characters towards our overflow! Sticking the address of the `backdoor` function on the end of your input, copy in about 256 characters (I didn't actually count, just added until it worked), and you'll `cat`'d the flag.

Command: `./cheip $(python -c 'print "can_you_do_bof" + "a"*246 + "\x08\x04\x84\xa4"[::-1]')`

Flag: `aoxl xvonsd ew we fnvo 0z9d z0ds-d8d8 0d0`
