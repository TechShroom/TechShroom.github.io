import pygame

class Render():
    def __init__(self, texture):
        self._texture_ = texture
        self._basetexture_ = texture

    def setTexture(self, newTexture):
        self._texture_ = newTexture
        self._basetexture_ = newTexture

    def getTexture(self):
        return self._texture_
    
    def getBaseTexture(self):
        return self._basetexture_

    def draw(self, screen, x = 0, y = 0):
        screen.blit(self._texture_, (x-self._texture_.get_width()/2,y-self._texture_.get_height()/2))

    def transform(self, scale = 1, rot = 0, flipX = False, flipY = False):
        self._texture_ = pygame.transform.rotozoom(self._basetexture_, rot, scale)
        self._texture_ = pygame.transform.flip(self._texture_, flipX, flipY)

    def reset():
        self._texture_ = self._basetexture_
