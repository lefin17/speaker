"""
2020-04-12 Testing endpoint class with adaptive method of spond detection with some voice properties
 
 + include module with endpoint class
 + open test wav file (whlole one / by blocks) 
 - plot dots 
 - plot energy without detect sound
 - plot sound detection signal
 - plot removing pauses in woice
 - plot removing parasite voice
 - plot adding of voice in ends of sound
 - save test sound
"""

#detect endpoints by energy

from endpoint import Endpoint 
import wave
import numpy as np
import sys

# test file s
filename = "w1.wav"

# create wave object 
wo = wave.open(filename, "r")
print (wo.getparams())
print (wo.getframerate())

# start to read file 
blk = wo.readframes(-1) # read whole file at one time (need to be small one) 
sgn = np.fromstring(blk, "Int16")

framelength = 256

print(sgn)
m = Endpoint(threshold=26, frequencyRate=wo.getframerate(), frameLength=framelength)

background = m.getBackground(sgn)
