import numpy as np
from scipy import signal
from scipy.fft import fftshift
import matplotlib.pyplot as plt
import wave 

fs = 44100
# N = 1e5

wav_file = "clarnet.wav"
wav_file = "c1.wav"
obj = wave.open(wav_file,'r')
print( "Number of channels",obj.getnchannels())
print ( "Sample width",obj.getsampwidth())
print ( "Frame rate.",obj.getframerate())
print ("Number of frames",obj.getnframes())
print ( "parameters:",obj.getparams())

N= obj.getnframes()
fs = obj.getframerate() 

amp = 2 * np.sqrt(2)
noise_power = 0.01 * fs / 2
time = np.arange(N) / float(fs)
# print (time)
# mod = 500*np.cos(2*np.pi*0.25*time)
# carrier = amp * np.sin(2*np.pi*3e3*time + mod)

# carrier = 
carrier = obj.readframes(-1)
carrier = np.fromstring(carrier, "Int16")
#noise = np.random.normal(scale=np.sqrt(noise_power), size=time.shape)
#noise *= np.exp(-time/5)
x = carrier #  + noise

# Compute and plot the spectrogram.
f, t, Sxx = signal.spectrogram(x, fs, nfft=2048, noverlap=1024, nperseg=2048)

obj.close()

plt.pcolormesh(t, f, Sxx)
plt.ylabel('Frequency [Hz]')
plt.xlabel('Time [sec]')
plt.show()