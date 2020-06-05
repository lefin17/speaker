import numpy as np
import wave
import sys
from endpoint import Endpoint

spf = wave.open("w1.wav", "r")

# y = []

# Extract Raw Audio from Wav File


BUFFER_SIZE = 2048 #multiplay of framelength

fp = wave.open('w1.wav', 'r')

#output = wave.open('output.wav', 'wb')
#output.setparams(fp.getparams())

frames_to_read = round(BUFFER_SIZE / (fp.getsampwidth() + fp.getnchannels()))

ep = Endpoint(threshold=-26, frequencyRate=44100, framelength=256)

while True:
    frames = fp.readframes(frames_to_read)
    if not frames:
        break
    ep.takeBuffer(frames)    

#    output.writeframes(frames)
#for x in range(0, 16):
#    y.append(x*x / 10)
    


# If Stereo
#if spf.getnchannels() > 1:
#    print("Just mono files")
#    sys.exit(0)

#plt.figure(1)
#plt.title("Signal Wave...")
#plt.plot(signal)
#plt.show()