(
	function($)
	{
		if(!$.fn.cycle)
		{
			if(window.console)
			{
				window.console.log('jQuery Cycle tiles: jQuery cycle plugin not found');
			}
			return;
		}

		$.fn.cycle.transitions.tiles =
			function($cont, $slides, opts)
			{
				opts.before
					.push(
						function(curr, next, opts)
						{
							$.fn.cycle.commonReset(curr, next, opts, true, true, true);
						}
					);

				// only adjust speed once!
				if (!opts.speedAdjusted)
				{
					opts.speed = opts.speed / 2; // each tile has a slight delay before its animation starts
					opts.speedAdjusted = true;
				}

				opts.tiles =
					$.extend(
						{
							cols: 1,
							order:
								function(i)
								{
									return i;
								},
							rows: 1,
						},
						opts.tiles
					);

				var
					i, j,
					numCols = opts.tiles.cols,
					numRows = opts.tiles.rows,
					len = numCols*numRows,
					height = $cont.css('overflow', 'hidden').height(),
					width = $cont.width(),
					tileWidth = Math.floor(width / numCols),
					lastTileWidth = width - (tileWidth*(numCols-1)),
					tileHeight = Math.floor(height / numRows),
					lastTileHeight = height - (tileHeight*(numRows-1)),
					nextTileAnimationDelay = opts.tiles.tileAnimationDelay;

				opts.tiles.tileAnimationDelay =
					opts.tiles.tileAnimationDelay
					|| 50;

				opts.tiles.cssBefore =
					$.extend(
						{
							left: 0,
							top: height
						},
						opts.tiles.cssBefore
					);

				opts.tiles.cssAfter =
					$.extend(
						{
							left: 0,
							top: 0
						},
						opts.tiles.cssAfter
					);

				opts.cssBefore =
					$.extend(
						{
							display: 'block',
							left: 0,
							top: 0
						},
						opts.cssBefore
					);

				opts.cssAfter =
					opts.cssAfter
					||
						{
							left: 0,
							top: 0
						};

				opts.fxFn =
					opts.fxFn
					||
						function(curr, next, opts, cb, fwd)
						{
							$('.cycle-tiles-container', $cont).addClass('cycle-tiles-obsolete').remove();

							var
								$curr = $(curr),
								$next = $(next),
								$tilesContainer = $('<div class="cycle-tiles-container" style="z-index: ' + $next.css('z-index') + ';" />').insertAfter($next),
								$tiles = $(),
								$tile,
								queueNext =
									function(i)
									{
										var
											iMapped = opts.tiles.order(i),
											x = iMapped % numCols,
											y = Math.floor(iMapped / numCols);

										$tiles.eq(iMapped)
											.animate(
												$.extend(
													opts.tiles.cssAfter,
													{
														height: (numRows-1===y) ? lastTileHeight : tileHeight,
														width: (numCols-1===x) ? lastTileWidth : tileWidth
													}
												),
												{
													duration: opts.speed,
													easing: opts.easeOut,
													complete:
														function()
															{
																if(fwd ? (len-1===i) : (0===i))
																{
																	$curr.hide();
																	if(!$tilesContainer.hasClass('cycle-tiles-obsolete'))
																	{
																		$next.show();
																	}
																	$tilesContainer.remove();
																	if (cb) cb();
																}
															}
												}
											)
											.find('> *')
												.fadeIn(opts.speed)
											.end();

										setTimeout(
											function()
											{
												if(fwd ? (len-1!==i) : (0!==i))
												{
													queueNext.apply(this, [fwd ? (i+1) : (i-1)]);
												}
											},
											nextTileAnimationDelay
										);
									};

							for(j=0; j < numRows; j++)
							{
								for(i=0; i < numCols; i++)
								{
									$tile =
										$('<div style="height: ' + ((numRows-1===j) ? lastTileHeight : tileHeight) + 'px; margin-left: ' + (i*tileWidth) + 'px; margin-top: ' + (j*tileHeight) + 'px; overflow: hidden; position: absolute; width: ' + ((numCols-1===i) ? lastTileWidth : tileWidth) + 'px; "/>')
											.css(opts.tiles.cssBefore)
											.append(
												$next.clone()
													.css(
														{
															'margin-left': -(i*tileWidth),
															'margin-top': -(j*tileHeight),
															'position': 'relative'
														}
													)
											);
									$tiles = $tiles.add($tile);
								}
							}
							$tilesContainer.append($tiles);

							$next.hide();

							$curr.css('z-index', 0);

							queueNext(fwd ? 0 : len-1);
						};

			};
	}
)(jQuery);