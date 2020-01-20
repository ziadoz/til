// Create a video with the path: ./media/test.mkv
package main

import (
	"log"
	"net/http"
	"text/template"
)

func main() {
	mux := http.NewServeMux()

	mux.HandleFunc("/media/", func(w http.ResponseWriter, r *http.Request) {
		h := http.StripPrefix("/media/", http.FileServer(http.Dir("./media")))
		h.ServeHTTP(w, r)
	})

	mux.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("content-type", "text/html")
		t := template.Must(template.New("index").Parse(`<video src="/media/test.mkv" type="video/mkv" controls style="width: 100%; height: 100%"></video>`))
		t.ExecuteTemplate(w, "index", map[string]string{})
	})

	log.Fatal(http.ListenAndServe(":8000", mux))
}
