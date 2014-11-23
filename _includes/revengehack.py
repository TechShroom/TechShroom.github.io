import gmpy2
from gmpy2 import mpz
from hash import sha1
message = "cat flag" # ls first time
messagesha = sha1(message).hexdigest() # sha1 of message
targetsig = "0001" + "f" * 8 + "00" + messagesha + "f" * 714 # target signature
targethex = int(targetsig, 16) # target signature, converted to an int
icbrt = gmpy2.iroot(mpz(targethex), 3) # integer cube root
# note: icbrt is a tuple of (result, exact).
# exact is True if it is a perfect cube root, False otherwise
perfectcube = icbrt[0] ** 3 # cube cube root
print(hex(perfectcube)) # print hex value, the result
