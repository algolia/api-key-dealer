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
  if (len(os.Args) != 2) {
    usage()
    os.Exit(1)
  }

  command := os.Args[1]

  if "start" == command {
    start()
  } else if "finish" == command {
    finish()
  } else {
    usage()
  }

}

func start() {
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

func finish() {
  printComment("TODO")
}

func usage() {
  fmt.Printf("Usage: %s [command]\n", os.Args[0])
  fmt.Println("Available commands")
  fmt.Println("\tstart\t\tGet Algolia credentials")
  fmt.Println("\tfinish\t\tDelete Algolia credentials")
  fmt.Println("\thelp\t\tPrint this message")
  fmt.Println()
}
