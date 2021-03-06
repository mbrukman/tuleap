describe("KanbanService -", function() {
    var $httpBackend,
        KanbanService,
        RestErrorService;

    beforeEach(function() {
        module('kanban', function($provide) {
            $provide.decorator('RestErrorService', function($delegate) {
                spyOn($delegate, "reload");

                return $delegate;
            });
        });

        inject(function(
            _$httpBackend_,
            _KanbanService_,
            _RestErrorService_
        ) {
            $httpBackend     = _$httpBackend_;
            KanbanService    = _KanbanService_;
            RestErrorService = _RestErrorService_;
        });

        installPromiseMatchers();
    });

    describe("reorderColumn() -", function() {
        var kanban_id,
            column_id,
            kanban_item_id,
            compared_to;

        beforeEach(function() {
            kanban_id      = 7;
            column_id      = 66;
            kanban_item_id = 996;
            compared_to    = {
                direction: 'after',
                item_id  : 268
            };
        });

        it("Given a kanban id, a column id, a kanban item id and a compared_to object, when I reorder the kanban item in the column, then a PATCH request will be made and a resolved promise will be returned", function() {
            $httpBackend.expectPATCH('/api/v1/kanban/' + kanban_id + '/items?column_id=' + column_id, {
                order: {
                    ids        : [kanban_item_id],
                    direction  : 'after',
                    compared_to: 268
                }
            }).respond(200);

            var promise = KanbanService.reorderColumn(
                kanban_id,
                column_id,
                kanban_item_id,
                compared_to
            );
            $httpBackend.flush();

            expect(promise).toBeResolved();
        });

        it("When there is an error with my request, then the error will be handled by RestErrorService and a rejected promise will be returned", function() {
            $httpBackend
                .expectPATCH('/api/v1/kanban/' + kanban_id + '/items?column_id=' + column_id)
                .respond(401, { error: 401, message: 'Unauthorized' });

            var promise = KanbanService.reorderColumn(
                kanban_id,
                column_id,
                kanban_item_id,
                compared_to
            );
            $httpBackend.flush();

            expect(promise).toBeRejected();
            expect(RestErrorService.reload).toHaveBeenCalledWith(jasmine.objectContaining({
                data: {
                    error  : 401,
                    message: 'Unauthorized'
                }
            }));
        });
    });

    describe("reorderBacklog() -", function() {
        var kanban_id,
            kanban_item_id,
            compared_to;

        beforeEach(function() {
            kanban_id      = 10;
            kanban_item_id = 194;
            compared_to    = {
                direction: 'before',
                item_id  : 181
            };
        });

        it("Given a kanban_id, a kanban item id and a compared_to object, when I reorder the kanban item in the backlog, then a PATCH request will be made and a resolved promise will be returned", function() {
            $httpBackend.expectPATCH('/api/v1/kanban/' + kanban_id + '/backlog', {
                order: {
                    ids        : [kanban_item_id],
                    direction  : 'before',
                    compared_to: 181
                }
            }).respond(200);

            var promise = KanbanService.reorderBacklog(
                kanban_id,
                kanban_item_id,
                compared_to
            );
            $httpBackend.flush();

            expect(promise).toBeResolved();
        });

        it("When there is an error with my request, then the error will be handled by RestErrorService and a rejected promise will be returned", function() {
            $httpBackend
                .expectPATCH('/api/v1/kanban/' + kanban_id + '/backlog')
                .respond(401, { error: 401, message: 'Unauthorized' });

            var promise = KanbanService.reorderBacklog(
                kanban_id,
                kanban_item_id,
                compared_to
            );
            $httpBackend.flush();

            expect(promise).toBeRejected();
            expect(RestErrorService.reload).toHaveBeenCalledWith(jasmine.objectContaining({
                data: {
                    error  : 401,
                    message: 'Unauthorized'
                }
            }));
        });
    });

    describe("reorderArchive() -", function() {
        var kanban_id,
            kanban_item_id,
            compared_to;

        beforeEach(function() {
            kanban_id      = 6;
            kanban_item_id = 806;
            compared_to    = {
                direction: 'after',
                item_id  : 620
            };
        });

        it("Given a kanban_id, a kanban item id and a compared_to object, when I reorder the kanban item in the archive, then a PATCH request will be made and a resolved promise will be returned", function() {
            $httpBackend.expectPATCH('/api/v1/kanban/' + kanban_id + '/archive', {
                order: {
                    ids        : [kanban_item_id],
                    direction  : 'after',
                    compared_to: 620
                }
            }).respond(200);

            var promise = KanbanService.reorderArchive(
                kanban_id,
                kanban_item_id,
                compared_to
            );
            $httpBackend.flush();

            expect(promise).toBeResolved();
        });

        it("When there is an error with my request, then the error will be handled by RestErrorService and a rejected promise will be returned", function() {
            $httpBackend
                .expectPATCH('/api/v1/kanban/' + kanban_id + '/archive')
                .respond(401, { error: 401, message: 'Unauthorized' });

            var promise = KanbanService.reorderArchive(
                kanban_id,
                kanban_item_id,
                compared_to
            );
            $httpBackend.flush();

            expect(promise).toBeRejected();
            expect(RestErrorService.reload).toHaveBeenCalledWith(jasmine.objectContaining({
                data: {
                    error  : 401,
                    message: 'Unauthorized'
                }
            }));
        });
    });

    describe("moveInColumn() -", function() {
        var kanban_id,
            column_id,
            kanban_item_id,
            compared_to;

        beforeEach(function() {
            kanban_id      = 1;
            column_id      = 88;
            kanban_item_id = 911;
            compared_to    = {
                direction: 'before',
                item_id  : 537
            };
        });

        it("Given a kanban id, a column id, a kanban item id and a compared_to object, when I move the kanban item to the column, then a PATCH request will be made and a resolved promise will be returned", function() {
            $httpBackend.expectPATCH('/api/v1/kanban/' + kanban_id + '/items?column_id=' + column_id, {
                add: {
                    ids: [kanban_item_id]
                },
                order: {
                    ids        : [kanban_item_id],
                    direction  : 'before',
                    compared_to: 537
                }
            }).respond(200);

            var promise = KanbanService.moveInColumn(
                kanban_id,
                column_id,
                kanban_item_id,
                compared_to
            );
            $httpBackend.flush();

            expect(promise).toBeResolved();
        });

        it("Given a null compared_to, when I add the kanban item to an empty column, then a PATCH request will be made and a resolved promise will be returned", function() {
            $httpBackend.expectPATCH('/api/v1/kanban/' + kanban_id + '/items?column_id=' + column_id, {
                add: {
                    ids: [kanban_item_id]
                }
            }).respond(200);

            var promise = KanbanService.moveInColumn(
                kanban_id,
                column_id,
                kanban_item_id,
                null
            );
            $httpBackend.flush();

            expect(promise).toBeResolved();
        });

        it("When there is an error with my request, then the error will be handled by RestErrorService and a rejected promise will be returned", function() {
            $httpBackend
                .expectPATCH('/api/v1/kanban/' + kanban_id + '/items?column_id=' + column_id)
                .respond(401, { error: 401, message: 'Unauthorized' });

            var promise = KanbanService.moveInColumn(
                kanban_id,
                column_id,
                kanban_item_id,
                compared_to
            );
            $httpBackend.flush();

            expect(promise).toBeRejected();
            expect(RestErrorService.reload).toHaveBeenCalledWith(jasmine.objectContaining({
                data: {
                    error  : 401,
                    message: 'Unauthorized'
                }
            }));
        });
    });

    describe("moveInBacklog() -", function() {
        var kanban_id,
            kanban_item_id,
            compared_to;

        beforeEach(function() {
            kanban_id      = 9;
            kanban_item_id = 931;
            compared_to    = {
                direction: 'after',
                item_id  : 968
            };
        });

        it("Given a kanban id, a kanban item id and a compared_to object, when I move the kanban item to the backlog, then a PATCH request will be made and a resolved promise will be returned", function() {
            $httpBackend.expectPATCH('/api/v1/kanban/' + kanban_id + '/backlog', {
                add: {
                    ids: [kanban_item_id]
                },
                order: {
                    ids        : [kanban_item_id],
                    direction  : 'after',
                    compared_to: 968
                }
            }).respond(200);

            var promise = KanbanService.moveInBacklog(
                kanban_id,
                kanban_item_id,
                compared_to
            );
            $httpBackend.flush();

            expect(promise).toBeResolved();
        });

        it("Given a null compared_to, when I add the kanban item to an empty backlog, then a PATCH request will be made and a resolved promise will be returned", function() {
            $httpBackend.expectPATCH('/api/v1/kanban/' + kanban_id + '/backlog', {
                add: {
                    ids: [kanban_item_id]
                }
            }).respond(200);

            var promise = KanbanService.moveInBacklog(
                kanban_id,
                kanban_item_id,
                null
            );
            $httpBackend.flush();

            expect(promise).toBeResolved();
        });

        it("When there is an error with my request, then the error will be handled by RestErrorService and a rejected promise will be returned", function() {
            $httpBackend
                .expectPATCH('/api/v1/kanban/' + kanban_id + '/backlog')
                .respond(401, { error: 401, message: 'Unauthorized' });

            var promise = KanbanService.moveInBacklog(
                kanban_id,
                kanban_item_id,
                compared_to
            );
            $httpBackend.flush();

            expect(promise).toBeRejected();
            expect(RestErrorService.reload).toHaveBeenCalledWith(jasmine.objectContaining({
                data: {
                    error  : 401,
                    message: 'Unauthorized'
                }
            }));
        });
    });

    describe("moveInArchive() -", function() {
        var kanban_id,
            kanban_item_id,
            compared_to;

        beforeEach(function() {
            kanban_id      = 4;
            kanban_item_id = 598;
            compared_to    = {
                direction: 'before',
                item_id  : 736
            };
        });

        it("Given a kanban id, a kanban item id and a compared_to object, when I move the kanban item to the archive, then a PATCH request will be made and a resolved promise will be returned", function() {
            $httpBackend.expectPATCH('/api/v1/kanban/' + kanban_id + '/archive', {
                add: {
                    ids: [kanban_item_id]
                },
                order: {
                    ids        : [kanban_item_id],
                    direction  : 'before',
                    compared_to: 736
                }
            }).respond(200);

            var promise = KanbanService.moveInArchive(
                kanban_id,
                kanban_item_id,
                compared_to
            );
            $httpBackend.flush();

            expect(promise).toBeResolved();
        });

        it("Given a null compared_to, when I add the kanban item to an empty archive, then a PATCH request will be made and a resolved promise will be returned", function() {
            $httpBackend.expectPATCH('/api/v1/kanban/' + kanban_id + '/archive', {
                add: {
                    ids: [kanban_item_id]
                }
            }).respond(200);

            var promise = KanbanService.moveInArchive(
                kanban_id,
                kanban_item_id,
                null
            );
            $httpBackend.flush();

            expect(promise).toBeResolved();
        });

        it("When there is an error with my request, then the error will be handled by RestErrorService and a rejected promise will be returned", function() {
            $httpBackend
                .expectPATCH('/api/v1/kanban/' + kanban_id + '/archive')
                .respond(401, { error: 401, message: 'Unauthorized' });

            var promise = KanbanService.moveInArchive(
                kanban_id,
                kanban_item_id,
                compared_to
            );
            $httpBackend.flush();

            expect(promise).toBeRejected();
            expect(RestErrorService.reload).toHaveBeenCalledWith(jasmine.objectContaining({
                data: {
                    error  : 401,
                    message: 'Unauthorized'
                }
            }));
        });
    });
});
