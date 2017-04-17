---
layout: post
title: PicoCTF 2017/Writeup - TW_GR_E4_STW (Toaster Wars 4)
---

This was a 200 point web exploitation problem.

This is a sequel to the 3 previous Toaster War game, and shares much of the same code.
There is a toaster character, which is the player, and some enemies to fight.
On the final floor, there is a flag item, like on the other games.

The catch for this game is that the stairs are inacessible on the penultimate floor,
making it impossible to actually get to the final floor and retrive the flag.

However, there is a "nifty" new scoreboard. While this change may not seem very important,
it opens up a bug which we can exploit. In [game.js](https://github.com/TechShroom/TechShroom.github.io/blob/master/picoctf2017/server/game.js),
on line 152, the code changed from storing the level in the MongoDB-stored `state` to using
the in-memory `db.scoreboard` object. This means that the map (stored in `state`) and the
level counter (stored in `db.scoreboard`) have a chance to be inconsistent, due to MongoDB
not using transactions and performing async writes for updates. This bug will let you use
the same stair location multiple times to artificially increase your level counter, which
then lets you jump to the final level straight from the first one.

In order to exploit this on the server, I used a slightly modified client that lets me hold down the
<kbd>ctrl</kbd> key to perform the amount of moves I need to jump to the last level from the first one.
A diff of the original client to my modified client is available [here](https://github.com/TechShroom/TechShroom.github.io/blob/master/picoctf2017/client.diff).

It takes a few tries to beat the race condition, but in my experience it takes no longer than 5 minutes to
get one run that jumps you straight to the last room, netting you the flag.
