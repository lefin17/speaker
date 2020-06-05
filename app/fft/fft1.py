import numpy as np
from scipy.fftpack import fft, ifft
x = np.arange(5)
print (x)
a = fft(x)
print (a)
T = np.allclose(fft(ifft(x)), x, atol=1e-15)  # within numerical accuracy.
print (T)