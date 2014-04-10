class Entity() :
    def __init__(this, world) :
        this.world = world
        this.setPos(0, 0)
        this.setDXY(0, 0)

    def getWorld(this) :
        return this.world

    def setX(this, x) :
        this.x = x

    def setY(this, y) :
        this.y = y

    def setPos(this, pos_x, y=None) :
        if type(pos_x) == tuple :
            x_ = pos_x[0]
            y_ = pos_x[1]
        else :
            x_ = pos_x
            y_ = y
        if y_ == None :
            raise TypeError("Non-tuple mode requires y")
        this.x = x_
        this.y = y_

    def getX(this) :
        return this.x

    def getY(this) :
        return this.y

    def getPos(this) :
        return (this.x, this.y)

    def setDX(this, dx) :
        this.dx = dx

    def setDY(this, dy) :
        this.dy = dy

    def setDXY(this, pos_dx, dy=None) :
        if type(pos_dx) == tuple :
            dx_ = pos_dx[0]
            dy_ = pos_dx[1]
        else :
            dx_ = pos_dx
            dy_ = dy
        if dy_ == None :
            raise TypeError("Non-tuple mode requires y")
        this.dx = dx_
        this.dy = dy_
        
    def getDX(this) :
        return this.dx

    def getDY(this) :
        return this.dy

    def getDXY(this) :
        return (this.dx, this.dy)

    def update(this, delta) :
        this.setX(this.x + delta * this.getDX())
        this.setY(this.y + delta * this.getDY())
