package main

import (
	"encoding/json"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"os"
	"strings"
	"time"
)

type Payload struct {
	TRAVIS_JOB_ID string
}

type Credentials struct {
	AppId        string `json:"app-id"`
	ApiKey       string `json:"api-key"`
	ApiSearchKey string `json:"api-search-key"`
	Mcm          struct {
		AppId  string `json:"app-id"`
		ApiKey string `json:"api-key"`
	} `json:"mcm"`
	Places struct {
		AppId  string `json:"app-id"`
		ApiKey string `json:"api-key"`
	} `json:"places"`
	Comment string `json:"comment"`
}

func main() {
	if len(os.Args) == 1 {
		usage()
	}

	command := os.Args[1]

	if "export" == command {
		export()
	} else {
		usage()
	}

}

func export() {
	credentials := getApiKey()

	cmd := fmt.Sprintf(
		"export ALGOLIA_APP_ID=%s ALGOLIA_APPLICATION_ID=%s ALGOLIA_API_KEY=%s ALGOLIA_SEARCH_API_KEY=%s",
		credentials.AppId,
		credentials.AppId,
		credentials.ApiKey,
		credentials.ApiSearchKey,
	)

	if credentials.Mcm.ApiKey != "" {
		cmd += fmt.Sprintf(
			" ALGOLIA_APP_ID_MCM=%s ALGOLIA_API_KEY_MCM=%s",
			credentials.Mcm.AppId,
			credentials.Mcm.ApiKey,
		)
	}

	if credentials.Places.ApiKey != "" {
		cmd += fmt.Sprintf(
			" ALGOLIA_APP_ID_PLACES=%s ALGOLIA_API_KEY_PLACES=%s",
			credentials.Places.AppId,
			credentials.Places.ApiKey,
		)
	}

	fmt.Print(cmd)
}

func getApiKey() Credentials {
	p := Payload{string(os.Getenv("TRAVIS_JOB_ID"))}
	jsonPayload, err := json.Marshal(p)

	req, err := http.NewRequest(
		"POST",
		"https://keys.algolia.engineering/1/travis/keys/new",
		strings.NewReader(string(jsonPayload)),
	)

	if err != nil {
		fmt.Printf("http.NewRequest() error: %v\n", err)
		os.Exit(100)
	}

	req.Header.Add("Content-Type", "application/json")

	client := &http.Client{
		Timeout: 15 * time.Second,
	}
	resp, err := client.Do(req)
	if err != nil {
		fmt.Printf("http.Do() error: %v\n", err)
		os.Exit(100)
	}
	defer resp.Body.Close()

	body, err := ioutil.ReadAll(resp.Body)

	if err != nil {
		log.Fatal(err)
	}

	credentials := Credentials{}
	err = json.Unmarshal(body, &credentials)
	if err != nil {
		log.Fatal("Invalid json response")
	}

	return credentials
}

func usage() {
	fmt.Printf("Usage: %s [command]\n", os.Args[0])
	fmt.Println("Available commands")
	fmt.Println("\texport\t\tExport Algolia credentials to env variables")
	fmt.Println("\thelp\t\tPrint this message")
	fmt.Println()

	os.Exit(0)
}
