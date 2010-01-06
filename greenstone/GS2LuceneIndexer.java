/**********************************************************************
 *
 * GS2LuceneIndexer.java 
 *
 * Copyright 2004 The New Zealand Digital Library Project
 *
 * A component of the Greenstone digital library software
 * from the New Zealand Digital Library Project at the
 * University of Waikato, New Zealand.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 *********************************************************************/

package org.greenstone.LuceneWrapper;


import java.io.*;
import java.util.Vector;

import org.xml.sax.Attributes;
import org.xml.sax.helpers.DefaultHandler;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;

import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.Term;
import org.apache.lucene.analysis.Analyzer;

import java.util.Stack;
import java.io.FileInputStream;
import java.io.File;
import java.io.StringReader;
import java.net.URL;


/**
 * class for indexing XML generated by lucenebuildproc.pm
  */

public class GS2LuceneIndexer {

    protected static boolean debug = false;

    protected static void debug(String message)
    {
	if (debug) {
	    System.err.println(message);
	}
    }


    public static void main (String args[]) throws Exception 
    { 
	int verbosity = 1;
	// Default is to edit the existing index
	boolean create_new_index = false;

	Vector filtered_args = new Vector();

	int argc = args.length;
	int i = 0;
	while (i<argc) {
	    if (args[i].startsWith("-")) {

		// -removeold causes the existing index to be overwritten
		if (args[i].equals("-removeold")) {
		    create_new_index = true;
		}

		// -verbosity [num]
		else if (args[i].equals("-verbosity")) {
		    i++;
		    if (i<argc) {
			verbosity = Integer.parseInt(args[i]);
			if (verbosity>=5) {
			    debug = true;
			}
		    }
		}
		else if (args[i].equals("-debug")) {
		    debug = true;
		}
		else {
		    System.out.println("Unrecognised option: " + args[i]);
		}
	    }
	    else {
		filtered_args.add((Object)args[i]);
	    }
	    i++;
	}

	if (filtered_args.size() != 3) {
	    System.out.println("Usage: java GS2LuceneIndexer [-removeold|-verbosity [num]] doc-tag-level building_dir index");
	    return;
	}
 
	String doc_tag_level     = (String)filtered_args.get(0);
	String building_dirname  = (String)filtered_args.get(1);
	String index_dirname     = (String)filtered_args.get(2);

	String import_dirname = building_dirname + File.separator + "text";

	File import_dir   = new File(import_dirname);
	File building_dir = new File(building_dirname);

	if (!import_dir.exists()) {
	    System.out.println("Couldn't find import directory: "+import_dirname);
	    return;
	}

	File idx_dir = new File(building_dir.getPath()+File.separator+index_dirname+File.separator);
	idx_dir.mkdir();
	
	// Set up indexer
	Indexer indexer = new Indexer(doc_tag_level, idx_dir, create_new_index);

	// Read from stdin the files to process
	try {
	    InputStreamReader isr = new InputStreamReader(System.in, "UTF-8");
	    BufferedReader brin = new BufferedReader(isr);

	    StringBuffer xml_text = new StringBuffer(1024);
	    String line = null;
	    while ((line = brin.readLine()) != null) {
		xml_text.append(line);
		xml_text.append(" ");

		debug("Got line " + line);

		if (line.endsWith("</Delete>")) {

		    indexer.delete(xml_text.toString());
		    xml_text = new StringBuffer(1024);		    
		}
		else if (line.startsWith("</Doc>")) {
		    indexer.index(xml_text.toString());
		    xml_text = new StringBuffer(1024);
		}
	    }

	    brin.close();
	    isr.close();

	} catch (IOException e) {
	    System.err.println("Error: unable to read from stdin");
	    e.printStackTrace();
	}

	indexer.finish();
    }


    static public class Indexer extends DefaultHandler 
    {
	IndexWriter writer_   = null;
	Analyzer analyzer_    = null;
	SAXParser sax_parser_ = null;
	String doc_tag_level_ = null;

	Stack stack_ = null;
	String path_ = "";

	Document current_doc_ = null;
	String current_node_ = "";
	String current_doc_oid_ = "";
	String indexable_current_node_ = "";
	String current_contents_ = "";

	String mode_ = "";
	protected String file_id_ = null;

	static private String[] stop_words = GS2Analyzer.STOP_WORDS;


	/** pass in true if want to create a new index, false if want to use the existing one */
	public Indexer (String doc_tag_level, File index_dir, boolean create) 
	{
	    doc_tag_level_ = doc_tag_level;

	    try {
		stack_ = new Stack();
		SAXParserFactory sax_factory = SAXParserFactory.newInstance();
		sax_parser_ = sax_factory.newSAXParser();

		XMLReader reader = sax_parser_.getXMLReader(); 
		reader.setFeature("http://xml.org/sax/features/validation", false);

		analyzer_ = new GS2Analyzer(stop_words);

		writer_ = new IndexWriter(index_dir.getPath(), analyzer_, create);
		// by default, will only index 10,000 words per document
		// Can throw out_of_memory errors
		writer_.setMaxFieldLength(Integer.MAX_VALUE);
		if (create) {
		    writer_.optimize();
		}
	    }
	    catch (Exception e) {
		// We need to know if creating/opening the index fails
		e.printStackTrace();
	    }
	}

	/** index one document */
	public void index (String file_id, File file) 
	{
	    mode_ = "add";
	    file_id_ = file_id;
	    path_ = "";
	    String base_path = file.getPath();
	    base_path = base_path.substring(0, base_path.lastIndexOf(File.separatorChar));

	    try {	    
		sax_parser_.parse(new InputSource(new FileInputStream(file)), this);
	    }
	    catch (Exception e) {
		println("parse error:");
		e.printStackTrace();
	    }
	}

	/** index one document stored as string*/
	public void index (String xml_text) 
	{
	    mode_ = "add";
	    file_id_ = "<xml doc on stdin>";
	    path_ = "";

	    try {
		sax_parser_.parse(new InputSource(new StringReader(xml_text)), this);
	    }
	    catch (Exception e) {
		println("parse error:");
		e.printStackTrace();
	    }
	}

	/** delete one document, based on doc_id in <Delete>doc_id</Delete> */
	public void delete(String xml_text) 
	{
	    mode_ = "delete";
	    file_id_ = "<delete doc>";
	    path_ = "";

	    try {
		sax_parser_.parse(new InputSource(new StringReader(xml_text)), this);
	    }
	    catch (Exception e) {
		println("parse error:");
		e.printStackTrace();
	    }
	}
    
	public void finish() 
	{
	    /** optimise the index */
	    try {
		writer_.optimize();
		writer_.close();
	    } 
	    catch (Exception e) {
	    }
	}

	protected void print(String s) 
	{ 
	    System.out.print(s); 
	}

	protected void println(String s) 
	{ 
	    System.out.println(s); 
	}

	public void startDocument() throws SAXException 
	{
	    println("Starting to process " + file_id_);
	    print("[");
	}

	public void endDocument() throws SAXException 
	{
	    println("]");
	    println("... processing finished.");
	}

	public void startElement(String uri, String localName, String qName, Attributes atts) 
	    throws SAXException 
	{
	    path_ = appendPathLink(path_, qName, atts);
	    
	    if (qName.equals(doc_tag_level_)) {
		mode_ = atts.getValue("gs2:mode");

		pushOnStack(); // start new doc
		current_node_ = qName;
		
		String node_id = atts.getValue("gs2:id");
		print(" " + qName + ": " + node_id + " (" + mode_ + ")" );
		current_doc_.add(new Field("nodeID", node_id, Field.Store.YES, Field.Index.UN_TOKENIZED));
		
		current_doc_oid_ = atts.getValue("gs2:docOID");
		current_doc_.add(new Field("docOID", current_doc_oid_, Field.Store.YES, Field.Index.UN_TOKENIZED));
	    }
	    
	    if (isIndexable(atts)) {
		indexable_current_node_ = qName;	
	    }
	    else {
		indexable_current_node_ = "";
	    }
	}

	public static boolean isIndexable(Attributes atts) 
	{
	    boolean is_indexable = false;

	    String index = atts.getValue("index");
	    if (index!=null) {
		if (index.equals("1")) {
		    is_indexable = true;
		}
	    }
	    return is_indexable;
	}

	public void endElement(String uri, String localName, String qName) throws SAXException 
	{
	    if (mode_.equals("delete")) {
		try {
		    deleteDocument(current_doc_oid_);
		}
		catch (java.io.IOException e) {
		    e.printStackTrace();
		}
	    }
	    else if (mode_.equals("add") || mode_.equals("update")) {
		if (qName.equals(indexable_current_node_))
		    {
        if (qName.equals("TX")) {
			current_doc_.add(new Field(qName, current_contents_, Field.Store.YES, Field.Index.TOKENIZED, Field.TermVector.YES));
        }
        else {
			current_doc_.add(new Field(qName, current_contents_, Field.Store.NO, Field.Index.TOKENIZED, Field.TermVector.YES));
        }
        
			// The byXX fields are used for sorting search results
			// We don't want to do that for Text or AllFields fields
			// They need to be untokenised for sorting
			if (!qName.equals("TX") && !qName.equals("ZZ"))
			    {
				current_doc_.add(new Field("by" + qName, current_contents_, Field.Store.NO, Field.Index.UN_TOKENIZED, Field.TermVector.NO));
			    }
			
			current_contents_ = "";
		    }
		
		if (qName.equals(doc_tag_level_)) {
		    try {
			// perhaps this is more efficient if addDocument()
			// used for "add" and updateDocument() for "update"
			writer_.updateDocument(new Term("docOID", current_doc_oid_), current_doc_, analyzer_);
		    } 
		    catch (java.io.IOException e) {
			e.printStackTrace();
		    }
		    popOffStack(); // end document
		}
		
		path_ = removePathLink(path_);
	    }
	}

	public void characters(char ch[], int start, int length) throws SAXException 
	{
	    String data = new String(ch, start, length).trim();
	    if (data.length() > 0 ) {
		current_contents_ += data;
	    }
	}
    
	protected String appendPathLink(String path, String qName, Attributes atts) 
	{

	    path = path + "/"+qName;
	    if (atts.getLength()>0) {
		String id = atts.getValue("gs2:id");
		if (id != null) {
		    path +=  "[@gs2:id='"+id+"']";
		}
		else {
		    id = atts.getValue("gs3:id");
		    if (id != null) {
			path +=  "[@gs3:id='"+id+"']";
		    }
		}
	    }
	    return path;
	}

	protected String removePathLink(String path) 
	{

	    int i=path.lastIndexOf('/');
	    if (i==-1) {
		path="";
	    } else {
		path = path.substring(0, i);
	    }
	    return path;
	}


	/** these are what we save on the stack */
	private class MyDocument 
	{
	    public Document doc = null;
	    public String contents = null;
	    public String tagname = "";
	
	}


	protected void pushOnStack() 
	{
	    if (current_doc_ != null) {
		MyDocument save = new MyDocument();
		save.doc = current_doc_;
		save.contents = current_contents_;
		save.tagname = current_node_;
		stack_.push(save);
	    }
	    current_doc_ = new Document();
	    current_contents_ = "";
	    current_node_ = "";
	}

	protected void popOffStack() 
	{
	    if (!stack_.empty()) {
		MyDocument saved = (MyDocument)stack_.pop();
		current_doc_ = saved.doc;
		current_contents_ = saved.contents;
		current_node_ = saved.tagname;
	    } else {
		current_doc_ = new Document();
		current_contents_ = "";
		current_node_ = "";
	    }
	}


	protected void deleteDocument(String doc_id)
	    throws IOException
	{
	    debug("GS2LuceneDelete.deleteDocument(" + doc_id + ")");
	    debug("- Initial number of documents in index: " + writer_.docCount());
	    writer_.deleteDocuments(new Term("docOID", doc_id));
	    debug("- Final number of documents in index: " + writer_.docCount());
	}


    }
}