from __future__ import print_function

dbins = dir(__builtins__)

def has(s) :
    return s in dbins
def rem(s) :
    before = eval(s)
    delattr(__builtins__, s)
    return before 

if has("xrange") :
    range = rem("xrange")
if has("raw_input") :
    input = rem("raw_input")

del has
del dbins
