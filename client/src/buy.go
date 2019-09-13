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
	TRAVIS_JOB_ID       string
	REPO_SLUG           string
	CIRCLE_BUILD_NUM    string
	CIRCLE_USERNAME     string
	CIRCLE_REPONAME     string
}

type Credentials struct {
	RequestId        string            `json:"request-id"`
	AppId            string            `json:"app-id"`
	ApiKey           string            `json:"api-key"`
	ApiSearchKey     string            `json:"api-search-key"`
	CtsAppId1        string            `json:"ALGOLIA_APPLICATION_ID_1"`
	CtsApiKey1       string            `json:"ALGOLIA_ADMIN_KEY_1"`
	CtsApiSearchKey1 string            `json:"ALGOLIA_SEARCH_KEY_1"`
	CtsAppId2        string            `json:"ALGOLIA_APPLICATION_ID_2"`
	CtsApiKey2       string            `json:"ALGOLIA_ADMIN_KEY_2"`
	Extra            map[string]string `json:"extra,omitempty"`
	Places           struct {
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

	cmd += fmt.Sprintf(
		" ALGOLIA_APPLICATION_ID_1=%s ALGOLIA_ADMIN_KEY_1=%s ALGOLIA_SEARCH_KEY_1=%s ALGOLIA_APPLICATION_ID_2=%s ALGOLIA_ADMIN_KEY_2=%s",
		credentials.CtsAppId1,
		credentials.CtsApiKey1,
		credentials.CtsApiSearchKey1,
		credentials.CtsAppId2,
		credentials.CtsApiKey2,
	)

	for k, v := range credentials.Extra {
		cmd += fmt.Sprintf(" %s=%s", k, v)
	}

	if credentials.Places.ApiKey != "" {
		cmd += fmt.Sprintf(
			" ALGOLIA_APP_ID_PLACES=%s ALGOLIA_API_KEY_PLACES=%s",
			credentials.Places.AppId,
			credentials.Places.ApiKey,
		)
	}

	cmd += " IS_COMMUNITY=true"

	cmd += " && echo " + getFormattedRequestId(credentials.RequestId)
	cmd += " && echo " + getFormattedComment(credentials.Comment)

	fmt.Print(cmd)
}

func getApiKey() Credentials {
	p := Payload{
		TRAVIS_JOB_ID:      os.Getenv("TRAVIS_JOB_ID"),
		REPO_SLUG:          os.Getenv("TRAVIS_REPO_SLUG"),
		CIRCLE_BUILD_NUM:   os.Getenv("CI_BUILD_NUM"),
		CIRCLE_USERNAME:    os.Getenv("CI_PROJ_USERNAME"),
		CIRCLE_REPONAME:    os.Getenv("CI_PROJ_REPONAME"),
	}
	jsonPayload, err := json.Marshal(p)

	url := "https://keys.algolia.engineering"

	dealerHost := os.Getenv("DEALER_HOST")
	if len(dealerHost) > 0 {
		url = "http://" + dealerHost
	}

	req, err := http.NewRequest(
		"POST",
		url+"/1/algolia/keys/new",
		strings.NewReader(string(jsonPayload)),
	)

	if err != nil {
		fmt.Printf("http.NewRequest() error: %v\n", err)
		os.Exit(100)
	}

	req.Header.Add("Content-Type", "application/json")

	client := &http.Client{
		Timeout: 90 * time.Second,
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
		log.Println(string(body))
		log.Println(err)
		os.Exit(101)
	}

	return credentials
}

func getFormattedComment(comment string) string {
	escaped := strings.Replace(comment, `'`, `'"'"'`, -1)
	return "' ~ Comment: " + escaped + " ~ '"
}

func getFormattedRequestId(reqId string) string {
	escaped := strings.Replace(reqId, `'`, `'"'"'`, -1)
	return "' ~ Request ID: " + escaped + " ~ '"
}

func usage() {
	fmt.Printf("Usage: %s [command]\n", os.Args[0])
	fmt.Println("Available commands")
	fmt.Println("\texport\t\tExport Algolia credentials to env variables")
	fmt.Println("\thelp\t\tPrint this message")
	fmt.Println()

	os.Exit(0)
}
