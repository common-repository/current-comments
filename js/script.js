/*
 * Current Comments - script.js
 */


jQuery( function( $ ) {
	Current_Comments = {};

	Current_Comments.Models = {
		Comment: Backbone.Model.extend( {
			defaults: {
				author : '',
				author_url : '',
				post_title : '',
				permalink : '',
				comment_date_gmt : 0
			}
		} )
	},

	Current_Comments.Collections = {
		Comments: Backbone.Collection.extend( {
			model: Current_Comments.Models.Comment,

			comparator: function( comment1, comment2 ) {
				return ( comment1.get( 'comment_date_gmt' ) < comment2.get( 'comment_date_gmt' ) ) ? -1 : 1;
			},

			getHighestCommentID: function() {
				var comment = _.max( this.models, function( model ) { return model.id; } );
				var maxID = ( "undefined" == typeof comment.id ) ? 0 : comment.id;
				console.log( maxID );
				return maxID;
			}

		})
	},

	Current_Comments.Views = {
		Comment: Backbone.View.extend( {
			model: Current_Comments.Models.Comment,

			tagName: 'li',

			className: 'current-comment-item',

			initialize: function() {
				this.model.on( 'change', this.render, this );
				this.model.on( 'destroy', this.remove, this );
			},

			render: function() {
				var template = _.template( "<a href='<%= author_url %>'><%= author %></a> on <a href='<%= permalink %>'><%= post_title %></a><br/><span class='current-comment-date' data-comment-date-gmt='<%= comment_date_gmt %>'></span>" );
				this.$el.html( template( this.model.attributes ) );
				this.$el.addClass( 'current-comment-' + this.model.get( 'id' ) ); // so we can find it later (removeOne)
				this.$el.find( '.current-comment-date' ).html( moment.unix( this.model.get( 'comment_date_gmt' ) ).fromNow() );

				return this;
			},

			remove: function() {
				this.$el.remove();
			}
		} ),

		Comments: Backbone.View.extend( {
			tagName: 'ul',

			initialize: function() {
				this.collection.on( 'add', this.addOne, this );
				this.collection.on( 'reset', this.render, this );
				this.collection.on( 'remove', this.removeOne, this );
				this.collection.on( 'sort', this.render, this );
			},

			render: function() {
				this.$el.html( '' );
				this.collection.forEach( this.addOne, this );
			},

			addOne: function( comment ) {
				var comment_view = new Current_Comments.Views.Comment( { model: comment } );
				this.$el.prepend( comment_view.render().el );
			},

			addAll: function() {
				this.collection.forEach( this.addOne, this );
			},

			removeOne: function( comment ) {
				this.$el.find( '.current-comment-' + comment.id ).remove();
			},

			updateChildren: function() {
				this.$el.find( '.current-comment-date' ).each( function() {
					var commentDateGmt = $( this ).data( 'comment-date-gmt' );
					$( this ).html( moment.unix( commentDateGmt ).fromNow() );
				} );			
			}
		} )
	},

	Current_Comments.Apps = {
		App : {
			initialize: function( el, ajaxurl ) {
				this.collection = new Current_Comments.Collections.Comments();
				this.collectionView = new Current_Comments.Views.Comments( { collection: this.collection, el : el } );
				this.ajaxurl = ajaxurl;
				this.requestUpdate();
			},

			requestUpdate: function() {
				this.collectionView.updateChildren();

				var highestCommentID = this.collection.getHighestCommentID();
				$.get( this.ajaxurl, { action: 'currcomm_read', newerthan: highestCommentID }, function( data ) {
					this.collection.add( data );
					setTimeout( function() {
						this.requestUpdate();
					}.bind( this ), 10000 );
				}.bind( this ));
			}
		}
	}
});

jQuery( document ).ready( function( $ ) {
	var el = $( '.current-comments-container' );
	if ( el.length ) {
		var app = Current_Comments.Apps.App;
		app.initialize( el, Current_Comments_Ajax.url );
	}
});