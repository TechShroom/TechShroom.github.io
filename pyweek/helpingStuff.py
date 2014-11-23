import pygame

def makeImage(image, colorkey = True, x = 0, y = 0):
    rawimg = pygame.image.load(image)
    if colorkey == True :
        rawimg.set_colorkey(rawimg.get_at((x, y)))
    return rawimg
