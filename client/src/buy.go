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
  ApiSearchKey  string `json:"api-search-key"`
  Mcm          struct {
  AppId  string `json:"app-id"`
  ApiKey string `json:"api-key"`
  } `json:"mcm"`
  Comment string `json:"comment"`
}

func main() {
  if (len(os.Args) == 1) {
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

  fmt.Printf(
    "export ALGOLIA_APP_ID=%s ALGOLIA_APPLICATION_ID=%s ALGOLIA_API_KEY=%s ALGOLIA_SEARCH_API_KEY=%s ALGOLIA_APP_ID_MCM=%s ALGOLIA_API_KEY_MCM=%s ",
    credentials.AppId,
    credentials.AppId,
    credentials.ApiKey,
    credentials.ApiSearchKey,
    credentials.Mcm.AppId,
    credentials.Mcm.ApiKey,
  )
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

func usage() {
  fmt.Printf("Usage: %s [command]\n", os.Args[0])
  fmt.Println("Available commands")
  fmt.Println("\texport\t\tExport Algolia credentials to env variables")
  fmt.Println("\thelp\t\tPrint this message")
  fmt.Println()
}
