package main

import (
	"fmt"
	"log"

	"github.com/ziadoz/gists/http-client-testing/pkg/gists"
)

func main() {
	gists, err := gists.NewApi().GetUserGists("ziadoz")
	if err != nil {
		log.Fatal(err)
	}

	fmt.Printf("%+v", gists)
}
