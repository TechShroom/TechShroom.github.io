---
layout: post
title: CodeGate 2015/Writeup - return
---

This was a 150 point miscellaneous problem.

The binary simply opens up the flag and reads it, then reads your input and calls `memcmp(flag, argv[1], strlen(argv[1]));`. It then sleeps for 3 seconds to prevent a brute force, and returns the result of `memcmp`.

By implementing a binary search using the return value of `return` as the compare, the flag can be narrowed down to its value.

Command: Something that did a binary search with `./return`.

Flag: `hohoqqqz`
