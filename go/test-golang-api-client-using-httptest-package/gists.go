package gists

import (
	"encoding/json"
	"fmt"
	"net/http"
)

type Api struct {
	client  *http.Client
	headers map[string]string
	BaseURL string
}

type Gist struct {
	Id  string `json:"id,omitempty"`
	Url string `json:"url,omitempty"`
}

func NewApi() *Api {
	return &Api{
		client:  &http.Client{},
		headers: map[string]string{"Accept": "application/vnd.github.v3+json"},
		BaseURL: "https://api.github.com",
	}
}

func (api *Api) GetUserGists(username string) ([]Gist, error) {
	request, err := http.NewRequest("GET", fmt.Sprintf("%s/users/%s/gists", api.BaseURL, username), nil)
	if err != nil {
		return []Gist{}, err
	}

	for k, v := range api.headers {
		request.Header.Set(k, v)
	}

	response, err := api.client.Do(request)
	if err != nil {
		return []Gist{}, err
	}

	defer response.Body.Close()

	gists := []Gist{}
	err = json.NewDecoder(response.Body).Decode(&gists)
	if err != nil {
		return []Gist{}, err
	}

	return gists, nil
}
