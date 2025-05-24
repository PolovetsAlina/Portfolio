<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.Connection" %>
<%@ page import="java.sql.DriverManager" %>
<%@ page import="java.sql.PreparedStatement" %>
<%@ page import="java.sql.SQLException" %>
<%@ page import="java.io.*" %>
<%@ page import="javax.servlet.http.Part" %>

<%
    HttpSession session = request.getSession(false);
    if (session == null || session.getAttribute("username") == null) {
        response.sendRedirect("index.jsp");
        return;
    }
    
    String username = (String) session.getAttribute("username");
    String role = (String) session.getAttribute("role");
    if (!"admin".equals(role)) {
        response.sendRedirect("area_riservata.jsp");
        return;
    }

    String title = request.getParameter("title");
    String director = request.getParameter("director");
    String year = request.getParameter("year");
    
    Part filePart = request.getPart("image");
    String fileName = filePart.getSubmittedFileName();
    
    String savePath = application.getRealPath("img") + File.separator + fileName;
    filePart.write(savePath);
    
    try {
        Class.forName("com.mysql.cj.jdbc.Driver");
        Connection conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
        
        // Insert new movie
        String sql = "INSERT INTO film (Titolo, Regista, Anno, Immagine) VALUES (?, ?, ?, ?)";
        PreparedStatement stmt = conn.prepareStatement(sql);
        stmt.setString(1, title);
        stmt.setString(2, director);
        stmt.setString(3, year);
        stmt.setString(4, fileName);
        
        int rows = stmt.executeUpdate();
        
        if (rows > 0) {
            response.sendRedirect("area_amministratore.jsp");
        } else {
            out.println("Errore nell'inserimento del film.");
        }
        
        stmt.close();
        conn.close();
    } catch (Exception e) {
        out.println("Errore: " + e.getMessage());
    }
%>
