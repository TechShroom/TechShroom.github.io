---
layout: post
title: PicoCTF 2014/Writeup - Pickle Jar
---
This was a Forensics challenge for 20 points.

The hack is very simple, just open up the JAR file, which is the same format as a ZIP file, in something like 7zip and read the pickled string from `pickle.p`. Doesn't even require opening Python, it's simple enough to read.

Flag: YOUSTOLETHEPICKLES
