from pygame.color import THECOLORS

def getcolor(name, defa=None) :
    name = name.lower()
    if name in THECOLORS :
        return THECOLORS[name]
    return defa

BLACK = getcolor("black", (0, 0, 0))
WHITE = getcolor("white", (255, 255, 255))
RED = getcolor("red", (255, 0, 0))
GREEN = getcolor("green", (0, 255, 0))
BLUE = getcolor("blue", (0, 0, 255))
