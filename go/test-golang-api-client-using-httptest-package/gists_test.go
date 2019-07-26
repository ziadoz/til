package gists

import (
	"net/http"
	"net/http/httptest"
	"testing"
)

func TestGetUserRepos(t *testing.T) {
	id := "repo1"
	url := "http://gists.github.com/repo1"

	handler := http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.WriteHeader(http.StatusOK)
		w.Write([]byte(`[{"id":"` + id + `","url":"` + url + `"}]`))
	})

	client := httptest.NewServer(handler)

	api := NewApi()
	api.BaseURL = client.URL

	gists, _ := api.GetUserGists("foobar")
	if len(gists) != 1 {
		t.Error("returned more than one gist")
	}

	if gists[0].Id != id {
		t.Errorf("gist.Id is not %s", id)
	}

	if gists[0].Url != url {
		t.Errorf("gist.Url is not %s", url)
	}
}
