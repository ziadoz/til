package main

import "fmt"

const (
	FIZZ     = 3
	BUZZ     = 5
	FIZZBUZZ = 15
)

func main() {
	for i := 0; i <= 100; i++ {
		result := fizzbuzz(i)
		fmt.Printf("%v %s\n", i, result)
	}
}

func fizzbuzz(num int) (str string) {
	switch {
	case num == 0:
		str += ""
	case num%FIZZBUZZ == 0:
		str += "FizzBuzz"
	case num%FIZZ == 0:
		str += "Fizz"
	case num%BUZZ == 0:
		str += "Buzz"
	}

	return str
}
