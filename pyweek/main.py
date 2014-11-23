# pygame :3
import pygame
from pygame.locals import *
# util
import helpingStuff
# colors
from color import *
import render

def updateFPS(clock):
    return clock.get_fps()

pygame.mixer.pre_init(44100, 16, 2, 4096)
pygame.init()
icon = helpingStuff.makeImage("techshroomicon.png")
ren = render.Render(icon)
pygame.display.set_icon(icon)
screen = pygame.display.set_mode((1000, 600))
pygame.display.set_caption('Pygaaaame!')

clock = pygame.time.Clock()

exitflag = False

i = 0

while not exitflag:
    for event in pygame.event.get():
        if event.type == QUIT:
            exitflag = True

    i += 1
    i%= 360
    ren.transform(rot=i*3, scale = i/60)
    pygame.display.update()
    screen.fill(WHITE)
    ren.draw(screen, screen.get_width()/2, screen.get_height()/2)
    clock.tick(60)
    pygame.display.set_caption('Pygaaaame! FPS: ' + str(int(updateFPS(clock))))

pygame.quit()
