from scipy import signal
import numpy as np
import matplotlib.pyplot as plt
from scipy.io import wavfile

#t = np.linspace(-1, 1, 500, endpoint=False)
#sig  = np.cos(2 * np.pi * 7 * t) + signal.gausspulse(t - 0.4, fc=2)
samplerate, source = wavfile.read("c1.wav")

sig = source
t = len(source) / samplerate
# widths = np.arange(1, 31)
widths = np.arange(1, 50)

cwtmatr = signal.cwt(sig, signal.ricker, widths)
plt.imshow(cwtmatr, extent=[0, t, 1, 31], cmap='PRGn', aspect='auto',
           vmax=abs(cwtmatr).max(), vmin=-abs(cwtmatr).max())
plt.show()