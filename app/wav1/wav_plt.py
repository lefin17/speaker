import matplotlib.pyplot as plt
import numpy as np
import wave
import sys


spf = wave.open("hello.l.wav", "r")

# y = []

# Extract Raw Audio from Wav File
signal = spf.readframes(-1)
signal = np.fromstring(signal, "Int16")

#for x in range(0, 16):
#    y.append(x*x / 10)
    


# If Stereo
if spf.getnchannels() > 1:
    print("Just mono files")
    sys.exit(0)

plt.figure(1)
plt.title("Signal Wave...")
plt.plot(signal)
plt.show()