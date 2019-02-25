<?php

namespace Lucid\Jaak\GraphQL;

class Mutation
{
    const RegisterDevice = 'mutation RegisterDevice($input: RegisterDeviceInput!) {
                                registerDevice(input: $input) {
                                  alreadyRegistered
                                  device {
                                    id
                                    createdAt
                                  }
                                }
                              }';
}