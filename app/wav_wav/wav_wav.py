# python3 

# some work with wav file

# idea of keys is taken here https://www.tutorialspoint.com/python/python_command_line_arguments.htm

# play audio is getting here... https://www.programcreek.com/python/example/82393/wave.open (only for python2.7) and need pyAudio that is not compilate

# for python3 https://pythonbasics.org/python-play-sound/

#white noice https://stackoverflow.com/questions/32237769/defining-a-white-noise-process-in-python

# Tranform wav file - to wav file segment
# -g gap of white noise in ms
# -x coordinate 
# -p pointer type right mid left
# -l length of image that preview file 
# -o output file (save in tmp and one of the answer is name of file) 
# -i input file
# -w width of segment in segments
# -t width of segment in ms
# - function - getWave - 

# roadmap 
#  + add amplitude of white noice gap into params
#  - quite execution 
#  - add scale and offset
#  - add selection as slow invert picture
# import wave

import os
import sys
from time import gmtime, strftime
import  getopt
import numpy

from pydub import AudioSegment
from pydub.playback import play

#get wave file with numpy format 
from scipy.io import wavfile 


# def get_output_file():
#    if not optputfile:
#        output_file = '../../assets/wav/'
#        output_file += strftime("%Y%m%d%H%M%S", gmtime()) + "_" + random(100, 999) + ".wav"
#    else:      
#        output_file = sys.argv[sys.argv.index("-o") + 1]



def main(argv):
    options = {}
    fn = ''
    try:
        opts, args = getopt.getopt(argv,"hi:o:w:t:g:x:p:l:s:f:q",["quite","function=","ifile=","ofile=","pointer=", "pix_width", "help", "width=", "time=", "gap=", "segment=", "start_point=", "play"])
    except getopt.GetoptError:
        print ('wav_wav.py --help to read about keys')
        sys.exit(2)
    for opt, arg in opts:
        if not arg: 
            continue
            
        if opt in ('-h', '--help'):
            print ('wav_wav.py -i <inputfile> -o <outputfile>')
            print ('-p, --pointer pointer type - left:default, mid or right')
            print ('-x, --start   coordinate in picture coords')
            print ('-w, --width   width to save in segments length')
            print ('-t, --time    width to save in ms')
            print ('-g, --gap     gap in both side with small white noise')
            print ('    --gap_amp gap amplitude (default 0.02 of max int16')
            print ('--pix_width   width of the imaage to sound')
            print ('-q, --quite   execute with silence on board, if it possible')
            print ('-f, --function some function to work with wav file')
            print ('        getWave - take small part of wave file')
            print ('        getSegment - return segment coordinates')
            print ('        play  - play input file name')
            print ('        wavInfo - get info wav (Sample rate, length, time')
          
            sys.exit()
        elif opt in ("-i", "--ifile"): #source file
            options["inputfile"] = arg
            
        elif opt in ("-o", "--ofile"): #destanation file
            options["outputfile"] = arg
            
        elif opt in ("-w", "--width"): #width of window in segments (int16)
            options["width"] = arg
            
        elif opt in ("-p", "--pointer"): #width of 
            options["pointer"] = arg
            
        elif opt in ("-t", "--time"): #time start 
            options["time"] = arg
            
        elif opt in ("-g", "--gap"): # gap in both sides
            options["gap"] = arg
            
        elif opt in ("pix_width"):
            options["pix_width"] = arg
        
        elif opt in ("-x", "--start_point"):
            options["start_point"] = arg    
                    
        elif opt in ("gap_amp"):
            options["gap_amp"] = arg    
                
        elif opt in ("-f", "--function"): # function wich started
            fn = arg
                
        
    if fn == '':
        print ('no function selected - start with --help')
        sys.exit(2)

    if not "inputfile" in options:    
        print ('ERROR: no input file, use --help or -i <input wav file>')
        sys.exit(2)
                    
    if not (os.path.exists(options["inputfile"]) and os.path.isfile(options["inputfile"])):
        print ('ERROR: no input file exists:' + options["inputfile"])
                        
    if fn == 'getWave':
        if not "outputfile" in options:
            print ("no output file '-o' key")
            sys.exit(2)    
        getWave(options)
    
    if fn == 'getSegment':
        getSegment(options)
        
    if fn == 'play':
        play_wav_file(options)
            
            
                    
#   print 'Input file is "', inputfile
#   print 'Output file is "', outputfile


   
   
def getWave(options):
    time = 0
    width = 0
    # 1st open file
    samplerate, source = wavfile.read(options["inputfile"])
    # print (fs)
    if "pix_width" in options:
        pix_width = options["pix_width"]
    else: 
        pix_width = 1024 # in pixels    
         
    if "width" in options: 
        width = int(options["width"])
             
    if "time" in options:
        time = int(options["time"])
        width = round(time / 1000 * samplerate)
        
    if not width:
        print ("to small period")
        sys.exit(2)
   
    if "start_point" in options:
        x = options["start_point"]
    else: 
        x = 0
#    else: time = 1000 # one second
            
    # truncate some info from file due to pointer type, x and width
    if not "pointer" in options or options["pointer"] == 'left':
        start = round(x / pix_width * samplerate)
    elif options["pointer"] == 'mid':
        start = round(x / pix_width * samplerate - width / 2)
    else: 
        start = round(x / pix_width * samplerate - width)
              
    stop = start + width 
    if not "quite" in options:
        print ("start point: {}".format(start))
        print ("stop point: {}".format(stop))
        print ("width: {}".format(width))
        source_len = len(source)
        print ("source len {}".format(source_len))
                
    if stop < len(source):    
        data = source[start:stop]
    else: 
        data = source[start:]    
    # add gap on both side with small white noice 
    if "gap" in options:
        num_samples = round(samplerate * options["gap"] / 1000)
        if "gap_amp" in (options):
            gap_amp = optinons["gap_amp"]
        else: 
            gap_amp = 0.02 
            
        amplitude = gap_amp * np.iinfo(np.int16).max
        mean = 0
        samples = numpy.random.normal(mean, amplitude, size=num_samples) 
        res = samples + data + samples 
    else: 
        res = data
    # save file
    wavfile.write(options["outputfile"], samplerate, res)
    pass

    
def getSegment(options):
    # Возвращает номера сегметов с которых будет взят звук
    pass    
    
    
#if not sys.argv.index("-p"):
#    pointer = "left";
#else: 
#    pointer = sys.argv[sys.argv.index("-p") + 1]
def wavInfo(options)
    #getting file info
    samplerate, source = wavfile.read(options["inputfile"])
    wdh = len(source);
    duration = wdh / samplerate;
    
    print ("File: {}".format(options["inputfile"]))
    print ("Sample Rate: {}".format(samplerate))
    print ("Samples in File: {}".format(wdh))
    print ("duration: {} sec".format(duration))
    
    
def play_wav_file(options):
    fname = options["inputfile"]
    song = AudioSegment.from_wav(fname)
    play(song)
    print ("play " + fname + " complite")
    
if __name__ == "__main__":
   main(sys.argv[1:])        