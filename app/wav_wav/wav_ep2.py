import numpy as np
import wave
import sys
from endpoint2 import EndpointFabric


BUFFER_SIZE = 2048 #multiplay of framelength

fp = wave.open('w1.wav', 'r')

ep = EndpointFabric({
                     "threshold" : -26, 
                     "freqRate": 44100, 
                     "frameLength": 256
                     })

while True:
    frames = fp.readframes(BUFFER_SIZE) # binary data
    data = np.frombuffer(frames, dtype='int16') # convert to int16 numpy array
    if not frames:
        break
 #   ep.test_energy(data)    #work with endpointFabric & endpoint classes 
 #   ep.test_isVoice(data)     #а звук ли?
 #   ep.test_front(data)
    ep.run(data) 
