#!/usr/bin/env python
import optparse, random, sys

def main():
	parser = optparse.OptionParser()
	parser.add_option('--min', type="int", default=0, help="The minimum number limit.")
	parser.add_option('--max', type="int", default=100, help="The maximum number limit.")
	opts, args = parser.parse_args()

	tries = 0
	number = random.randint(opts.min, opts.max)
		
	sys.stdout.write("Guess The Number \n")
	sys.stdout.write("I'm thinking of a number between %d and %d. \n\n" % (opts.min, opts.max))
		
	while True:
		if tries == 0:
			sys.stdout.write("Take a guess: ")
		else:
			sys.stdout.write("Try again: ")
		
		try:
			tries += 1
			guess = int(raw_input().strip())
		except ValueError:
			sys.stdout.write("It helps if you enter a number. \n")
			continue
		
		if guess < opts.min:
			sys.stdout.write("It can't be less than %d now can it! \n" % opts.min)
		elif guess > opts.max:
			sys.stdout.write("It can't be more than %d now can it! \n" % opts.max)
		elif guess < number:
			sys.stdout.write("Try going higher. \n")
		elif guess > number:
			sys.stdout.write("Try going lower. \n")
		else:
			sys.stdout.write("Correct, it was %d! \n" % number)
			sys.stdout.write("It took you %d guesses. \n" % tries)
			sys.exit(0)
		
if __name__ == '__main__':
	main()