#! /bin/bash
arecord -D 'plughw:1,0' -d $1 -r 16000 -f S16_LE ./wav/test.wav
#设置libmsc.so库搜索路径
export LD_LIBRARY_PATH=$(pwd)/../libs/RaspberryPi/
./iat_sample
