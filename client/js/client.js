$(window).addEvent('domready', function() {
	var rpc = new JSONRPC.getClient('/~scato/openchess/json.php?Game');
	
	var sessionId = rpc.call('startSession', []);
	var name = prompt('Name:');
	var player = rpc.call('createPlayer', [sessionId, name]);
	var opponent, game, board;
	
	new EJS({url: 'client/waiting.ejs'}).update(document.body, {});
	findOpponent();
	
	function findOpponent() {
		opponent = rpc.call('getOpponentInfo', [sessionId]);
		
		if(opponent === null) {
			try {
				opponent = rpc.call('findOpponent', [sessionId]);
				startGame();
			} catch(e) {
				setTimeout(findOpponent, 500);
			}
		} else {
			startGame();
		}
	}
	
	function startGame() {
		game = rpc.call('getGameInfo', [sessionId]);
		
		if(game === null) {
			try {
				game = rpc.call('startGame', [sessionId]);
				setupBoard();
			} catch(e) {
				setTimeout(startGame, 500);
			}
		} else {
			setupBoard();
		}
	}
	
	function setupBoard() {
		board = new EJS({url: 'client/board.ejs'});
		
		renderBoard();
	}
	
    function renderBoard() {
        game.findPieceByPosition = function(file, rank) {
            var found = null;
    
            this.pieces.forEach(function(piece) {
                if(piece.position.file === file && piece.position.rank === rank) {
                    found = piece;
                }
            });
    
            return found;
        };

        game.findPlayerByColor = function(color) {
            if(color === 'PIECE_COLOR_WHITE') {
                return this.white;
            } else {
                return this.black;
            }
        };
        
        game.findPlayerToMove = function() {
            if(this.state === 'GAME_STATE_WHITE_TO_MOVE') {
                return this.white;
            } else if(this.state === 'GAME_STATE_BLACK_TO_MOVE') {
                return this.black;
            } else {
                return null;
            }
        };
        
        board.update(document.body, {player: player, game: game});
        
        if(game.findPlayerToMove() && game.findPlayerToMove().id === player.id) {
            setTimeout(updateBoard, 0);
        } else {
            setTimeout(loadBoard, 500);
        }
    }
    
    function loadBoard() {
        game = rpc.call('getGameInfo', [sessionId]);
        renderBoard();
    }
    
    var handlers = [];
    
    function resetSquares() {
        var files = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        var ranks = [1, 2, 3, 4, 5, 6, 7, 8];
        
        files.forEach(function(file) {
            ranks.forEach(function(rank) {
                var square = document.getElementById(file + rank);
                
                square.className = square.className.replace(/ (?:target|destination|active)/g, '');
            });
        });
        
        var handler;
        while(handler = handlers.pop()) {
            handler.element.removeEventListener(handler.type, handler.listener, false);
        }
    }
    
    function setupChooseTarget() {
        var files = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        var ranks = [1, 2, 3, 4, 5, 6, 7, 8];

        files.forEach(function(file) {
            ranks.forEach(function(rank) {
                var piece = game.findPieceByPosition(file, rank);
                
                if(piece && game.findPlayerByColor(piece.color).id === player.id && piece.validMoves.length > 0) {
                    var listener;
                    var square = document.getElementById(file + rank);
                    square.className += ' target';
                    square.addEventListener('click', listener = function() {
                        chooseTarget(piece);
                    }, false);
                    handlers.push({element: square, type: 'click', listener: listener});
                }
            });
        });
    }
    
    function setupChooseMove() {
        var file = target.position.file;
        var rank = target.position.rank;
        
        var listener;
        var square = document.getElementById(file + rank);
        square.className += ' active';
        square.addEventListener('click', listener = function() {
            chooseTarget(null);
        }, false);
        handlers.push({element: square, type: 'click', listener: listener});
        
        target.validMoves.forEach(function(move) {
            var file = move.destination.file;
            var rank = move.destination.rank;
            
            var listener;
            var square = document.getElementById(file + rank);
            square.className += ' destination';
            square.addEventListener('click', listener = function() {
                chooseMove(move);
            }, false);
            handlers.push({element: square, type: 'click', listener: listener});
        });
    }
    
    var target = null;
    
    function chooseTarget(piece) {
        target = piece;
        resetSquares();
        updateBoard();
    }
    
    function chooseMove(move) {
        game = rpc.call('makeMove', [sessionId, player.id, game.id, target.position.file, target.position.rank, move.destination.file, move.destination.rank]);
        target = null;
        resetSquares();
        renderBoard();
    }
    
    function updateBoard() {
        if(target === null) {
            setupChooseTarget();
        } else {
            setupChooseMove();
        }
    }
});

