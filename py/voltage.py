import os.path #проверка на наличие файла для чтения
import wave # работа с wave файлом


class Voltage:
    
# Задача класса генерировать jpeg с характеристикой входного аудиофайла

    
    def __init__(self):
	self.background = "#fff" # цвет фона изображения
	self.color = "#e5e" # цвет графика
	self.unit = "sec" # по какой величене идет округление
	self.units = 1 # сколько частей в округлении
	# варианты - jpg, array
	
	self.mode = "jpg" # файл может работать на вывод в качестве картинки, а может и массив отдавать для прорисовки в js
	self.fn = "AVG" # варианты - либо найти среднее, либо максимальное
	# self.fn = "MAX" 
	
	self.width = 1024 # длина картинки в пикселях (это если про картинку)
	self.height = 100 # высота
	
	self.grid = True # рисовать ли решетку
	self.gridWidth = 1 # растояние между линиями в секундах
	self.gridColor = "#ccc"
	
	self.subGrid = True # рисовать ли сетку второго порядка
	self.subGridWidth = 0.2 # через какое расстояние рисовать сетку второго порядка
	self.subGridColor = "#eee"
	self.currentChannel = 1 #текущий канал воспроизведения
	
	
    def loadFile(self, filename):
	if os.path.isfile(filename):
	    self.filename = filename
    
    def waveInfo(self):
	if not os.path.isfile(self.filename):
	    return
	obj = wave.open(self.filename,'r')
	print("Number of channels",obj.getnchannels())
	print ("Sample width",obj.getsampwidth())
	print ("Frame rate.",obj.getframerate())
	print ("Number of frames",obj.getnframes())
	print ("parameters:",obj.getparams())
	obj.close() 
	    	
    
