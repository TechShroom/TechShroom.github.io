---
layout: post
title: PicoCTF 2014/Writeup: Revenge of the Bleichenbacher
---
This was a crypto challenge for 170 points. In order to get the flag, you need to forge a RSA signature for your command.

To start this, my teammate John sent me http://www.intelsecurity.com/resources/wp-berserk-analysis-part-1.pdf. This is a paper explaining the exploit that Bleichenbacher described. I read it over, and tried to use their python code to generate a forged signature. I didn’t know what BITLEN, `forge_prefix`’s `s` parameter, `target_EM_middle_mask` and a few other things meant. I messed around with them, guessed BITLEN was the length of the signature in bits, pulled the mask to get the right EM from the paper, and eventually figured out that `s` was the target signature, the one you wanted to get back after the cube root. `forge_prefix()` was pretty much an efficient cube root finder. You gave it the signature, it gave you back a perfect cube. What differed from what the paper says is that `forge_prefix()` needs `s` to be the *final* signature. Eventually I figured out that the paper was really off from what I needed for this particular problem. This paper deals with the ASN.1 encoding, while the given JAR file doesn’t deal with ASN.1 at all.

So I wrote a bunch of new code to generate what should be a valid signature for the service, and kept testing it on the JAR until it passed. This only took a few minutes once I realized how it checked everything. It simply takes what you gave it, cube roots that value to get the signature to check, pads it with zeros in front until the length is 768 (the size of N), and then verifies that signature. The verification process is really simple, and if I had ignored the Intel paper, I would have gotten this problem much quicker.

Verification is done by first checking that the signature starts with `0001ffffffffff` (`0001` followed by 8 `f`s). After that, it checks that the signature is exactly 768 characters long and that the signature contains `sha1(command)`. Then it reads through the string until it reaches 2 characters before the `sha1` index. All characters from the first `f` until the end of the current read must be `f`. The 2 characters before the `sha1` but after the `f`s must be `00`. Then comes the sha1, which is simply just 40 characters. That is when it says “this is valid”.

So let’s review the whole verification requirements:
`0001` + `”f” * 8` + more `f`s? + `00` + sha1 (40 chars) = 768 characters
4 + 8 + ? + 2 + 40 = 768
? = 714 extra `f`s

However, this doesn’t check that there’s nothing after the sha1. So you can actually do:
`0001` + `”f” * 8` + `00` + sha1 (40 chars) + 714 of anything = 768 characters
That’s a pretty big flaw. Those 714 garbage characters are enough to generate a perfect cube. So, the hack is simply creating `0001` + `”f” * 8` + `00` + sha1 (40 chars) + `”f” * 714`, and then having something like gmpy give you the floored cube root of that number (this works because the floored cube root has a perfect cube that is valid). Cube that, and you have your forged signature!
