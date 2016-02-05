function stripHTML(dirtyString) {
 return dirtyString.replace(/(<([^>]+)>)/ig,"");
}
