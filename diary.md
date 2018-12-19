# Challenge Diary

## Day 1

That was trivial. No real challenge.

## Day 2

The example on the first part had me confused. I was trying to keep track if
a character had a double or triple overall. In the example the results are the
same as the intended, just 0 or 1 double and 0 or 1 triple per box ID.

## Day 3

Part 2: The second was so much simpler than part 1. It was just used a box collider
to find the claim that didn't collide with any others. I wanted to use it for part 1,
but the overlap amount was difficult, so scrapped that for a more brutish method.

## Day 4

## Day 5

I fought with this one so hard trying to get a regex with back references to work, but
in the end I couldn't figure out how to deal with the case issue. So just created all
56 combinations and did a iterative search and replace.

## Day 6

## Day 7

## Day 8

I felt lucky with today's puzzle. It took me a while to understand the tree,
but was able to get the code right on the first try both times. I guess this
made up for Day 7.

## Day 9

Part1:

Part2: After noticing how long the first part ran with only 72,000 iterations, it
seemed like it was purely sadistic to make the iterations 100x more. That or see
how fast of computers the top 100 have. With practically no code change, the time
between answers for part 1 and part 2 would be based solely on computing power.

But then I read in the subreddit, that arrays are slow for this and should use a 
linked list or similar data structure. Well closest in PHP is SplDoublyLinkedList
which was still slooooow.

Ended up writing a LinkList class. The run time of the Part 1 with this method
took 0.09827 seconds, 10x took 3.17794 seconds, and 100x took 255.98302 seconds.
However, I had to increase the memory limit to 4GB to get it to finished 100x.

And four hours after the original Part 2 was kicked off, it still hadn't finished.
Neither had the SplDoublyLinkedList that was started about an hour after that.

EDIT: Refactor of the LinkList class allowed part 2 to run in 166.37396 seconds
with only 3GB of RAM.

## Day 10

Argh! The example text was 8 rows high, but the real answer is 10 rows high.
The example also shows the message converges on 0,0 as the top left of the 
message. No such luch. /sigh

I did some manual work on this one. Once the lights got were within a 300x300
grid, I started looking at the output. And I looked like it might be a multi-line 
message. So I wasted a bunch of time with that (because I was looking for text
that were 8 rows high). I ended up just checking the rows of "data" every 100 records
then stepping through manually. Was irked when I saw the message converge over
10 rows.

After submitting the solution - and part two since I was tracking that info already - 
I went to the subreddit and realized I should have just kept track of the vertical size
and output the smallest.

This is one I want to go back and refactor to make it purely automated.

## Day 11

Thanks to Computerphile for helping me solve this... https://www.youtube.com/watch?v=uEJ71VlUmMQ

Part 1 was fairly easy, but like a lot of these Part 2 forces you to use more
optimized solutions unless you have infinite time or memory. Once I remembered about
the computerphile video, watched it, and implemented it. It ran smooth.

## Day 12

Watching my visualization turn to the same thing after a number of iterations, I realised 
I could just do some math to calculate the solution from that point, so didn't have to
run for billions of generations.

## Day 13

This was the challenge that as of this date I did the best (911/653) on the leaderboard.

## Day 14

Part 2 description could have used some help.

## Day 15

Part 1: I've never done any kind of pathfinding, so that was interesting.

Part 2: With the way I set up my Unit class, it was trivial to update it so a
AP power could be passed in. Then just itterate of the simulation until no
Elf deaths.

## Day 16

Reminded me a bit of the DCPU-16 that Notch created for the ill-fated game 0x10c

## Day 17

Recursion is key.

## Day 18

Return of the game of life, this time in 2D, checking for gliders and offset to goal.

## Day 19

First part cake. Second part had to go to the forums for some hints. Then looked at the input
some and saw that it was trying to get the factors of a register. Manually summed them with
some help from https://www.calculatorsoup.com/calculators/math/factors.php

Almost feel like I cheated a bit

