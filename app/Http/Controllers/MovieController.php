<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Models\Movie;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovieController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderBy = $request->get('orderBy');

        if ($orderBy && $orderBy === 'DESC') {
            $movies = Movie::orderByDesc('name')->get();
        } else {
            $movies = Movie::orderBy('name')->get();
        }

        return $this->sendResponse(MovieResource::collection($movies), 'Movies listed successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'file' => 'required|mimes:mp4,ogx,oga,ogv,ogg,webm|max:5120'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validations error.', $validator->errors());
        }

        $file = $request->file('file');

        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('uploads', $fileName, 'public');

        $input['file_size'] = $file->getSize();
        $input['file'] = '/storage/' . $filePath;

        $movie = Movie::create($input);

        return $this->sendResponse(new MovieResource($movie), 'Movie created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function show(Movie $movie)
    {
       //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function edit(Movie $movie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Movie $movie)
    {
        $input = $request->all();

        if (!empty($input['tags']) && is_array($input['tags'])) {
            $movie->tags()->sync($input['tags']);
        }

        if (!empty($input['name'])) {
            $movie->name = $input['name'];
        }

        $movie->save();

        return $this->sendResponse(new MovieResource($movie), 'Movie updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movie $movie)
    {
        $movie->tags()->sync([]);
        $movie->delete();

        return $this->sendResponse([], 'Movie deleted successfully.');
    }
}
