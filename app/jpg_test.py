# make simple jpeg with diagonal line

from PIL import Image, ImageDraw

# some color constants for PIL

white = (255, 255, 255)
black = (0, 0, 0)
grey = (200, 200, 200)

blue = (0, 0, 255)
red = (255, 0, 0)
green = (0,128,0)

width = 450
height = 450

image = Image.new("RGB", (width, height), white)
draw = ImageDraw.Draw(image)


for k in range(0, 500, 50): 
    draw.line((0, k, 450, k), grey)
    draw.line((k, 0, k, 450), grey)

# draw horizontal lines
""" x1 = 0
x2 = 450
for k in range(0, 500, 50):
    y1 = k
    y2 = k
    # PIL (to memory for saving to file)
    draw.line((x1, y1, x2, y2), grey)    

# draw vertical lines
y1 = 0
y2 = 450
for k in range(0, 500, 50):
    x1 = k
    x2 = k
    draw.line((x1, y1, x2, y2), grey)
    """

filename = "mylines1.jpg"
image.save(filename)
