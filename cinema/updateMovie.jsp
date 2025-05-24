<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.Connection" %>
<%@ page import="java.sql.DriverManager" %>
<%@ page import="java.sql.PreparedStatement" %>
<%@ page import="java.sql.ResultSet" %>
<%@ page import="java.sql.SQLException" %>
<%@ page import="java.util.ArrayList" %>
<%@ page import="java.util.List" %>
<%@ page import="java.util.Enumeration" %>
<%@ page import="java.util.Collection" %>
<%@ page import="java.io.File" %>
<%@ page import="javax.servlet.http.Part" %>
<%@ page import="java.io.IOException" %>
<%@ page import="javax.servlet.ServletException" %>
<%@ page import="javax.servlet.annotation.MultipartConfig" %>
<%@ page import="javax.servlet.http.HttpServletRequest" %>
<%@ page import="java.io.InputStream" %>
<%@ page import="java.io.InputStreamReader" %>
<%@ page import="java.io.BufferedReader" %>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiornamento Film - Cinema Hub</title>
    <style>
        .error-message {
            color: red;
            font-weight: bold;
        }
        .debug-info {
            background-color: #f0f0f0;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            font-family: monospace;
        }
        body {
            background-color: #333;
            color: #fff;
        }
        .success-message {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
<%
    Connection conn = null;
    PreparedStatement pstmt = null;
    ResultSet rs = null;
    
    try {
        // Debug film code sources
        out.println("DEBUG - Film code sources:<br>");
        out.println("From filmCode param: " + request.getParameter("filmCode") + "<br>");
        out.println("From code param: " + request.getParameter("code") + "<br>");
        
        // Recupera il codice film in questo ordine:
        // 1. Dal parametro filmCode
        // 2. Dalla sessione
        // 3. Dal parametro code (backward compatibility)
        String code = request.getParameter("filmCode");
        if (code == null || code.trim().isEmpty()) {
            code = (String) session.getAttribute("currentFilmCode");
        }
        if (code == null || code.trim().isEmpty()) {
            code = request.getParameter("code");
        }
        
        // Validazione avanzata codice film
        if (code == null || code.trim().isEmpty()) {
            session.setAttribute("error", "ERRORE CRITICO: Nessun codice film ricevuto. Contattare l'amministratore.");
            response.sendRedirect("area_amministratore.jsp");
            return;
        }
        
        code = code.trim().toUpperCase();
        out.println("DEBUG - Using film code: " + code + "<br>");
        
        // Debug information
        out.println("<div class='debug-info'>");
        out.println("<h3>Informazioni Debug:</h3>");
        out.println("<p>Codice ricevuto: '" + code + "'</p>");
        out.println("<p>Tipo di codice: " + (code == null ? "null" : "stringa") + "</p>");
        
        // Debug avanzato - mostra tutti i parametri ricevuti
        out.println("<div class='debug-info'>");
        out.println("<h3>Parametri ricevuti:</h3>");
        java.util.Enumeration<String> params = request.getParameterNames();
        while(params.hasMoreElements()) {
            String paramName = params.nextElement();
            out.println("<p>" + paramName + ": '" + request.getParameter(paramName) + "'</p>");
        }
        out.println("</div>");

        // Verifica connessione al database
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
            conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
            
            // Verifica esistenza nel database
            String checkSql = "SELECT COUNT(*) FROM film WHERE Codice_film = ?";
            PreparedStatement checkStmt = conn.prepareStatement(checkSql);
            checkStmt.setString(1, code);
            ResultSet checkRs = checkStmt.executeQuery();
            checkRs.next();
            if (checkRs.getInt(1) == 0) {
                session.setAttribute("error", "ATTENZIONE: Tentativo di modificare un film inesistente (codice: " + code + ")");
                response.sendRedirect("area_amministratore.jsp");
                return;
            }
        } catch(Exception e) {
            out.println("<div class='error-message'>");
            out.println("Errore database: " + e.getMessage());
            out.println("</div>");
            return;
        }
        
        // Get all parts from the request for other fields
        Collection<Part> parts = request.getParts();
        
        // Process each part
        String newTitle = null;
        String newDirector = null;
        String newYear = null;
        Part newImage = null;
        
        for (Part part : parts) {
            String partName = part.getName();
            String partValue = null;
            
            try {
                if (partName.equals("title")) {
                    InputStream is = part.getInputStream();
                    BufferedReader br = new BufferedReader(new InputStreamReader(is));
                    newTitle = br.readLine();
                    br.close();
                    partValue = newTitle;
                } else if (partName.equals("director")) {
                    InputStream is = part.getInputStream();
                    BufferedReader br = new BufferedReader(new InputStreamReader(is));
                    newDirector = br.readLine();
                    br.close();
                    partValue = newDirector;
                } else if (partName.equals("year")) {
                    InputStream is = part.getInputStream();
                    BufferedReader br = new BufferedReader(new InputStreamReader(is));
                    newYear = br.readLine();
                    br.close();
                    partValue = newYear;
                } else if (partName.equals("newImage")) {
                    newImage = part;
                    partValue = "File: " + part.getSubmittedFileName();
                }
                
                out.println("<p>Parametro: " + partName + " = " + partValue + "</p>");
                
            } catch (Exception e) {
                out.println("<p>Errore nel parsing del parametro " + partName + ": " + e.getMessage() + "</p>");
            }
        }

        // Prepare the update query based on what fields were actually provided
        StringBuilder updateSql = new StringBuilder("UPDATE film SET ");
        List<Object> paramsList = new ArrayList<>();
        boolean hasUpdates = false;
        
        if (newTitle != null && !newTitle.trim().isEmpty()) {
            updateSql.append("Titolo = ?, ");
            paramsList.add(newTitle.trim());
            hasUpdates = true;
        }
        
        if (newDirector != null && !newDirector.trim().isEmpty()) {
            updateSql.append("Regista = ?, ");
            paramsList.add(newDirector.trim());
            hasUpdates = true;
        }
        
        if (newYear != null && !newYear.trim().isEmpty()) {
            try {
                int year = Integer.parseInt(newYear.trim());
                updateSql.append("Anno = ?, ");
                paramsList.add(year);
                hasUpdates = true;
            } catch (NumberFormatException e) {
                out.println("<div class='error-message'>");
                out.println("Errore: L'anno deve essere un numero");
                out.println("</div>");
                return;
            }
        }
        
        if (newImage != null) {
            InputStream is = newImage.getInputStream();
            updateSql.append("Immagine = ?, ");
            paramsList.add(is);
            paramsList.add(newImage.getSize());
            hasUpdates = true;
        }
        
        // If no fields were changed, show a message and return
        if (!hasUpdates) {
            out.println("<div class='success-message'>");
            out.println("Nessun campo è stato modificato");
            out.println("</div>");
            response.sendRedirect("area_amministratore.jsp");
            return;
        }
        
        // Remove trailing comma and add WHERE clause
        if (updateSql.toString().endsWith(" ")) {
            updateSql.deleteCharAt(updateSql.length() - 2);
        }
        updateSql.append(" WHERE Codice_film = ?");
        paramsList.add(code);

        // Debug the final query
        out.println("<p>Query finale: " + updateSql.toString() + "</p>");
        out.println("<p>Parametri: " + paramsList.toString() + "</p>");

        // Execute the update
        pstmt = conn.prepareStatement(updateSql.toString());
        
        int paramIndex = 1;
        for (int i = 0; i < paramsList.size(); i++) {
            if (paramsList.get(i) instanceof InputStream) {
                // Handle image upload
                InputStream is = (InputStream) paramsList.get(i);
                long size = (long) paramsList.get(i + 1);
                pstmt.setBinaryStream(paramIndex++, is, size);
                i++; // Skip the size parameter
            } else {
                pstmt.setObject(paramIndex++, paramsList.get(i));
            }
        }

        int rowsUpdated = pstmt.executeUpdate();
        
        if (rowsUpdated > 0) {
            out.println("<div class='success-message'>");
            out.println("Film aggiornato con successo!");
            out.println("</div>");
            
            // Redirect to admin area after successful update
            response.sendRedirect("area_amministratore.jsp");
            return;
        } else {
            out.println("<div class='error-message'>");
            out.println("Nessun film aggiornato. Query eseguita: " + updateSql);
            out.println("</div>");
        }
    } catch (Exception e) {
        out.println("<div class='error-message'>");
        out.println("Errore: " + e.getMessage());
        out.println("</div>");
        e.printStackTrace();
        return;
    } finally {
        if (pstmt != null) {
            try { pstmt.close(); } catch (Exception e) {}
        }
        if (conn != null) {
            try { conn.close(); } catch (Exception e) {}
        }
    }
%>
</body>
</html>
