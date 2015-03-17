---
layout: post
title: CodeGate 2015/Writeup - urandom
---

This was a 100 point miscellaneous problem.

The given program reads 10 bytes off of `/dev/urandom` and compares it to your input. The length of the compare is the length of the bytes from urandom.

Since C uses the null byte to determine the end of a string, and urandom will occasionally return that null byte, the actual length of the string is sometimes 1. Simply passing in one character repeatedly will eventually award you the flag.

Command: `while true; do ./urandom a; done`

Flag: `ch!!!zzola`
