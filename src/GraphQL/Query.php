<?php

namespace LucidTunes\Jaak\GraphQL;

class Query
{
    const ListTracks = 'query ListTracks {
                          application {
                            tracks {
                              edges {
                                track {
                                  id
                                  licenseID
                                  title
                                  artist
                                  releaseDate
                                  duration
                                  genres {
                                    name
                                  }
                                  asset {
                                    url
                                  }
                                }
                              }
                            }
                          }
                        }';

    const ListApplication = 'query { application { id } }';
}