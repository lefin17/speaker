# read wav, make jpg, with avg and disperspersion

from PIL import Image, ImageDraw
import wave
import math 
import array
import numpy

black = (0, 0, 0)
gray = (125,125,125)
white = (255, 255, 255)
width = 1024
height = 256

def plotY(y):
    height_file = 65536/2
    res = -y/height_file*height/2+height/2
    # print (res)
    return res
    


wav_file = "hello.l.wav"

obj = wave.open(wav_file,'r')
print( "Number of channels",obj.getnchannels())
print ( "Sample width",obj.getsampwidth())
print ( "Frame rate.",obj.getframerate())
print ("Number of frames",obj.getnframes())
print ( "parameters:",obj.getparams())


image = Image.new("RGB", (width, height), white)
draw = ImageDraw.Draw(image)

NinFr = math.ceil(obj.getnframes() / width)

print ('N in one step: ' + str(NinFr)) 

# for k in range(width):
k = 0
x = 1
d = 0;
c = [] #array()    
for j in range(obj.getnframes()):
    a = obj.readframes(1)
#    b = int.from_bytes(a, byteorder='little') # little_endian data storage
    b = int(numpy.frombuffer(a, dtype='int16'))
    c.append(b)
    k += 1
    draw.point((x, plotY(b)), gray)
    if (k >= NinFr):
        x += 1
        d0 = d
        d = numpy.mean(c)     
         
        c = []
        k = 0
        draw.line(((x - 1), plotY(d0), x, plotY(d)), black)
        # print (b)
        # print (d)
   
    
   # print(a)
    pass

obj.close() #close wav file 

filename = "wav_hello.jpg"
image.save(filename)

