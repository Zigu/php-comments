const root = document.body

const loadCommentsRequest = function (vnode) {
    return m.request({
        method: "GET",
        url: "/comments"
    }).then(function (result) {
        vnode.state.data.comments = result;
    }).catch(function (e) {
        vnode.state.data.errors = [e.message]
    })
};

const loadCommentRequest = function (vnode) {
    vnode.state.data.errors = [];
    return m.request({
        method: "GET",
        url: "/comments/:id",
        params: {id: vnode.attrs.id}
    }).then(function (result) {
        vnode.state.data.currentComment = result;
    }).catch(function (e) {
        vnode.state.data.errors = [e.message]
    })
};

const updateCommentRequest = function (vnode) {
    return m.request({
        method: "PUT",
        url: "/comments/:id",
        params: {id: vnode.state.data.currentComment.id},
        body: vnode.state.data.currentComment
    }).then(function (result) {
        vnode.state.data.currentComment = result;
        m.route.set('/list');
    }).catch(function (error) {
        if (error.code == 404) {
            vnode.state.data.errors = ['Could not update comment. Comment not found.'];
        } else if (error.code == 400) {
            vnode.state.data.errors = [];
            if (error.response.fieldErrors.author) {
                vnode.state.data.errors.push(error.response.fieldErrors.author);
            }
            if (error.response.fieldErrors.text) {
                vnode.state.data.errors.push(error.response.fieldErrors.text);
            }
        } else {
            vnode.state.data.errors = [e.message];
        }
    })
};

const createCommentRequest = function (vnode) {
    return m.request({
        method: "POST",
        url: "/comments",
        body: vnode.state.data.newComment
    }).then(function () {
        m.route.set('/list')
    }).catch(function (error) {
        if (error.code == 404) {
            vnode.state.data.errors = ['Could not update comment. URL not found.'];
        } else if (error.code == 400) {
            vnode.state.data.errors = [];
            if (error.response.fieldErrors.author) {
                vnode.state.data.errors.push(error.response.fieldErrors.author);
            }
            if (error.response.fieldErrors.text) {
                vnode.state.data.errors.push(error.response.fieldErrors.text);
            }
        } else {
            vnode.state.data.errors = [error.message];
        }
    })
};

function deleteComment(vnode, comment) {
    m.request({
        method: "DELETE",
        url: "/comments/:id",
        params: {id: comment.id}
    }).then(function () {
        const index = vnode.state.data.comments.indexOf(comment);
        if (index > -1) {
            vnode.state.data.comments.splice(index, 1); // 2nd parameter means remove one item only
        }
    }).catch(function (error) {
        if (error.code == 404) {
            vnode.state.data.errors = ['Could not update comment. URL not found.'];
        } else {
            vnode.state.data.errors = [e.message];
        }
    })
}

const CommentsList = {
    data: {
        comments: [],
        errors: []
    },
    oninit: loadCommentsRequest,
    view: function (vnode) {
        return m("div.pure-g", [
            m('.pure-u-1.create-comment-link', [m(m.route.Link, {href: "/create"}, 'Create comment')]),
            m('.pure-u-1', [m('ul.errors.pure-u', vnode.state.data.errors.map(function (error) {
                return m("li", error)
            }))]),
            m(".comments-list.pure-u-1", vnode.state.data.comments.map(function (comment) {
                return m(".comment", [
                    m('.text', comment.text),
                    m('.meta', 'by ' + comment.author + ', created at ' + comment.createdAt + ', updated at ' + comment.updatedAt),
                    m('.actions', [
                        m(m.route.Link, {href: "/edit/" + comment.id,}, 'Edit'),
                        m('button.delete.pure-button', {
                            onclick: function (e) {
                                deleteComment(vnode, comment);
                            }
                        }, 'Delete')
                    ])
                ])
            }))
        ]);
    }
};

const CreateComment = {
    data: {
        newComment: {},
        errors: []
    },
    oninit: function (vnode) {
        vnode.state.data.newComment = {};
        vnode.state.data.errors = [];
    },
    view: function (vnode) {
        return m("form.commentForm.pure-form.pure-form-stacked", {
                onsubmit: function (e) {
                    e.preventDefault();
                    createCommentRequest(vnode);
                }
            },
            [
                m('ul.errors', vnode.state.data.errors.map(function (error) {
                    return m("li", error)
                })),
                m('fieldset', [
                    m("label.label[for=author]", "Author"),
                    m("input#author[type=text][placeholder=Author]", {
                        oninput: function (e) {
                            vnode.state.data.newComment.author = e.target.value
                        },
                        value: vnode.state.data.newComment.author
                    }),
                    m("label.label[for=text]", "Text"),
                    m("textarea#text[placeholder=text]", {
                        oninput: function (e) {
                            vnode.state.data.newComment.text = e.target.value
                        },
                        value: vnode.state.data.newComment.text
                    }),
                    m(".actions", [
                        m("button.pure-button.pure-button-primary[type=submit]", "Save"),
                        m(m.route.Link, {href: "/list"}, "Back to list")
                    ])

                ])
            ])
    }
};

const EditComment = {
    data: {
        currentComment: {},
        errors: []
    },
    oninit: loadCommentRequest,
    view: function (vnode) {
        return m("form.commentForm.pure-form.pure-form-stacked", {
                onsubmit: function (e) {
                    e.preventDefault();
                    updateCommentRequest(vnode);
                }
            },
            [
                m("ul.errors", vnode.state.data.errors.map(function (error) {
                    return m("li", error)
                })),
                m("fieldset", [
                    m("label[for=author]", "Author"),
                    m("input.#author[type=text][placeholder=Author]", {
                        oninput: function (e) {
                            vnode.state.data.currentComment.author = e.target.value
                        },
                        value: vnode.state.data.currentComment.author
                    }),
                    m("label[for=text]", "Text"),
                    m("textarea#text[placeholder=text]", {
                        oninput: function (e) {
                            vnode.state.data.currentComment.text = e.target.value
                        },
                        value: vnode.state.data.currentComment.text
                    }),
                    m("div.actions", [
                        m("button.pure-button.pure-button-primary[type=submit]", "Save"),
                        m(m.route.Link, {href: "/list"}, "Back to list")
                    ])
                ])
            ])
    }
};

m.route(root, "/list", {
    "/list": CommentsList,
    "/create": CreateComment,
    "/edit/:id": EditComment,
});
