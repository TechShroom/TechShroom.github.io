from socket import socket
import re
import itertools
import pickle
def gcd(x, y):
   """This function implements the Euclidian algorithm
   to find G.C.D. of two numbers"""

   while(y):
       x, y = y, x % y

   return x
regparse = re.compile('Welcome to the Daedalus Corp Spies RSA Key Generation Service\\. The public modulus you should use to send your updates is below\\. Remember to use exponent 65537\\.\\n([a-fA-F0-9]+)\\n', re.MULTILINE)
def scrape_number():
    s = socket()
    s.connect(('vuln2014.picoctf.com', 51818))
    parse = s.recv(8192) + s.recv(8192)
    mat = parse.decode('ascii')
    return regparse.match(mat).group(1)
numberspicked = 'numbers.pickle'
somenumbers = set()
def request_ns(count):
    global somenumbers, combs
    somenumbers |= {int(scrape_number(), 16) for i in range(count)}
    combs = itertools.combinations(somenumbers, 2)
    with open(numberspicked, 'wb') as pickledata:
        pickle.dump(somenumbers, pickledata)
with open(numberspicked, 'rb') as pickledata:
    somenumbers = pickle.load(pickledata)
    combs = itertools.combinations(somenumbers, 2)

