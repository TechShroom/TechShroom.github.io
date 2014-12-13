---
layout: post
title: PicoCTF 2014/Writeup - Low Entropy
---
This was a Cryptography challenge for 110 points.

You are given a .pcap with some data and the source for the keygeneration server. The goal is to get the private key so you can decode the data to the flag.

The key server generates `N` from a set of 30 primes and gives it to you. The key server also tells you to use exponent 65567, so we don't need to find `e`. Since we don't know the 2 primes used for the pcap, we need to find them from the set of 30 primes. Unfortunately, only the remote server knows the 30 primes.

However, since 1) they're primes and 2) there's a limited set, if we generate enough `N`s from the remote server we can figure out the primes involved with each `N` quickly because there's only one set of numbers that can multiply to `N`. If we do this enough times we can get all 30 primes. The file that I used to do this is [here (prime_harder.py)](prime_harder.py). It uses pickle to store the data, so we don't need to do any copy pasting if we don't want to. There's no need to really do much with it, load it into IDLE or import it as a module and run `request_ns(100)`. This should net you about 80-100 `N`s, you can check the length of `somenumbers` to verify. Ignore the file not found on startup for the first time.

After collecting the `N`s from prime_harder.py, we can try to break the pcap data by selecting two primes from the set. There are 435 combinations possible, so this won't take too long. My breaker for this is [here (rsa_harder.py)](rsa_harder.py). Running this takes less than 10 seconds, netting us the flag.

Flag: make_sure_your_rng_generates_lotsa_primes

Aside: You can get a pregenerated numbers.pickle [here](numbers.pickle).
