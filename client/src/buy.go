package main

import (
  "fmt"
  "log"
  "net/http"
  "io/ioutil"
  "encoding/json"
  "strings"
  "os"
)

type Payload struct {
    TRAVIS_JOB_ID string
}

type Credientials struct {
	AppId   string `json:"app-id"`
	ApiKey  string `json:"api-key"`
	Comment string `json:"comment"`
}

func main() {
  credentials := getApiKey()

  printComment(credentials.Comment)

  f, err := os.Create("./.env.travis")
  if err != nil {
      panic(err)
  }

  env := fmt.Sprintf(
    "ALGOLIA_APP_ID=%s\nALGOLIA_API_KEY=%s",
    credentials.AppId,
    credentials.ApiKey,
  )

  f.WriteString(env)
}

func printComment(comment string) {
  fmt.Println(comment)
}

func getApiKey() Credientials {
  p := Payload{string(os.Getenv("TRAVIS_JOB_ID"))}
  jsonPayload, err := json.Marshal(p)

  req, err := http.NewRequest(
    "POST",
    "http://api-key-dealer.herokuapp.com/1/travis/keys/new",
    strings.NewReader(string(jsonPayload)),
  )

  if err != nil {
    fmt.Printf("http.NewRequest() error: %v\n", err)
    os.Exit(100)
  }

  req.Header.Add("Content-Type", "application/json")

  client := &http.Client{}
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

  credentials := Credientials{}
  err = json.Unmarshal(body, &credentials)
  if err != nil {
      log.Fatal("Invalid json response")
  }

  return credentials
}
