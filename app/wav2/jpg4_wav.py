# read wav, make jpg, with avg and disperspersion

from PIL import Image, ImageDraw
import wave
import math 
import array
import numpy
import sys

black = (0, 0, 0)
gray = (125,125,125)
grey = (200,200,200)
white = (255, 255, 255)
width = 1024
height = 256

def plotY(y):
    height_file = 65536/2
    res = -y/height_file*height/2+height/2
    # print (res)
    return res
    

if not sys.argv[1]:
    wav_file = "hello.l.wav"
else: 
    wav_file = sys.argv[1]    

obj = wave.open(wav_file,'r')
print( "Number of channels",obj.getnchannels())
print ( "Sample width",obj.getsampwidth())
print ( "Frame rate.",obj.getframerate())
print ("Number of frames",obj.getnframes())
print ( "parameters:",obj.getparams())


image = Image.new("RGB", (width, height), white)
draw = ImageDraw.Draw(image)

NinFr = math.ceil(obj.getnframes() / (width) * 8)

print ('N in one step: ' + str(NinFr)) 

# for k in range(width):
k = 0
x = 1
d = 0;
c2 = []
c1 = []

c = [] #array()    
for j in range(obj.getnframes()):
    k += 1
    a = obj.readframes(1)
#    b = int.from_bytes(a, byteorder='little') # little_endian data storage
    b = int(numpy.frombuffer(a, dtype='int16'))
#    print (b)
    if b >= 0:
        c1.append(b)
    else:
        c2.append(b)
		
#    draw.point((x, plotY(b)), gray)
    if (k >= NinFr):
        x += 1
        d0 = d
        d1 = numpy.max(c1)
        if not c2:
            d2 = 0
            av2 = 0
        else:
            d2 = numpy.min(c2)
            av2 = numpy.mean(c2)     
        av1 = numpy.mean(c1)
             
        c1 = []
        c2 = []
        k = 0
        # Рисуем усредненное по верхней половине и нижней половине...  четыре пикселя пробел, две полоски рядом и серые вокруг для смягчения
        draw.line(((x - 1)*8 , plotY(0), (x*8), plotY(0)), black)
#        print("d1 - " + str(d1))
        print(str(x) + ":" + str(d1) + ":" + str(d2))
        draw.line(((x-1)*8 + 3, plotY(av2), (x-1)*8 + 3, plotY(av1)), gray)
        draw.line(((x-1)*8 + 4, plotY(d2), (x-1)*8 + 4, plotY(d1)), grey)
        draw.line(((x-1)*8 + 5, plotY(d2), (x-1)*8 + 5, plotY(d1)), grey)
        draw.line(((x-1)*8 + 6, plotY(av2), (x-1)*8 + 6, plotY(av1)), gray)
        # print (b)
        # print (d)
   
    
   # print(a)
    pass

obj.close() #close wav file 


if not sys.argv[2]:
    filename = "wav_hello_2.jpg"
else:    
    filename = sys.argv[2]
image.save(filename)

