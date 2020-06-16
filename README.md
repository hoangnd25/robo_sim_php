[![Coverage Status](https://coveralls.io/repos/github/hoangnd25/robo_sim_php/badge.svg)](https://coveralls.io/github/hoangnd25/robo_sim_php)

Robot Simulator
===================

This is an CLI application requires PHP 7.4 and above.
It accepts a list of space separated commands (see example bellow)

    ./console robot "PLACE 1,2,EAST" MOVE MOVE LEFT MOVE REPORT

    Options:
      -s, --size        Set board size [default: "5"]
      -v, --verbose     Show logs
